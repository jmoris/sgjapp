<?php

namespace App\Console;

use App\AEC;
use App\Cesion;
use App\DocumentoPendiente;
use App\Factura;
use App\FacturaCompra;
use App\GuiaDespacho;
use App\Helpers\Ajustes;
use App\ListaCorreo;
use App\Mail\ReporteDiarioPendientes;
use App\NotaCredito;
use App\NotaCreditoCompra;
use App\Notifications\DocumentoRecibido;
use App\Permiso;
use App\Services\ComprasUnificadasSyncService;
use App\Services\FacturapiService;
use App\User;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Tenant;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        try {
            $tenants = Tenant::all();
        } catch (Exception $ex) {
            // Si no se puede resolver la lista de tenants (BD landlord caída, etc.) NO se debe
            // dejar que la excepción aborte `schedule:run`: eso deja TODO el cron sin ejecutar
            // (incluida la sincronización de compras) hasta que alguien lo note.
            Log::error("No se pudo obtener la lista de tenants para el scheduler: ".$ex->getMessage());
            return;
        }

        foreach($tenants as $tenant){
          try {
            $tenant->makeCurrent();
            if (Tenant::checkCurrent()) {
                if (empty($tenant->facturapi_token)) {
                    Log::warning("Tenant sin credenciales de FacturAPI v3 asignadas, las tareas que dependan de la API fallarán con warning: ".$tenant->name);
                }
                /**
                 * Tarea que revisa el estado de los documentos pendientes
                 */
                $schedule->call($tenant->callback(function() {
                    try{
                        $facturapi = new FacturapiService();
                        $pendientes = DocumentoPendiente::all();
                        foreach ($pendientes as $doc) {
                            Log::info("Consultando estado SII: tipo_doc={$doc->tipo_doc} folio={$doc->folio} track_id={$doc->track_id}");
                            $docData = $facturapi->consultarEstadoSii((string) $doc->track_id);
                            Log::info("Respuesta consulta-estado: ".json_encode($docData));
                            $respData = $docData->data ?? null;
                            if(($docData->success ?? false) && $respData != null){
                                $estado = '0';
                                if(($respData->aceptados ?? 0) == 1||($respData->reparos ?? 0) == 1){
                                    // documento aceptado
                                    $estado = '1';
                                }else if(($respData->rechazados ?? 0) == 1){
                                    $estado = '2';
                                }
                                if($doc->tipo_doc == 33){
                                    $fact = Factura::where('folio', $doc->folio)->first();
                                    $factEstado = $fact->estado;
                                    $factEstado = substr($factEstado, 1);
                                    $newEstado = $estado.$factEstado;

                                    Log::info($newEstado);

                                    Factura::where('folio', $doc->folio)->update([
                                        'track_id' => $doc->track_id,
                                        'estado' => $newEstado
                                    ]);
                                    DocumentoPendiente::find($doc->id)->delete();
                                }
                                if($doc->tipo_doc == 52){
                                    Log::info("Actualizando guia de despacho folio={$doc->folio} estado={$estado}");
                                    $filasActualizadas = GuiaDespacho::where('folio', $doc->folio)->update([
                                        'track_id' => $doc->track_id,
                                        'estado' => $estado
                                    ]);
                                    if($filasActualizadas === 0){
                                        Log::warning("No se encontro guia de despacho con folio={$doc->folio} para actualizar estado");
                                    }
                                    DocumentoPendiente::find($doc->id)->delete();
                                }

                                if($doc->tipo_doc == 61){
                                    $nc = NotaCredito::where('folio', $doc->folio)->first();
                                    $ncEstado = $nc->estado;
                                    $ncEstado = substr($ncEstado, 1);
                                    $newEstado = $estado.$ncEstado;
                                    NotaCredito::where('folio', $doc->folio)->update([
                                        'track_id' => $doc->track_id,
                                        'estado' => $newEstado
                                    ]);
                                    DocumentoPendiente::find($doc->id)->delete();
                                }
                            }

                        }
                    }catch(Exception $ex){
                        Log::error("Error revisando el estado en el SII de los documentos generados: ".$ex->getMessage());
                    }
                }))->everyFifteenMinutes();

                /**
                 * Tarea que revisa el estado del correo de los documentos generados
                 */
                $schedule->call($tenant->callback(function() {
                    try{
                        $facturapi = new FacturapiService();
                        // Facturas
                        $pendientes = Factura::where('estado', 'regexp', '[0-3]0[0|1]')->get();
                        foreach ($pendientes as $doc) {
                            $docData = $facturapi->consultarEstadoCorreo(33, $doc->folio);
                            $estado = strval($doc->estado);
                            $estadoSii = substr($estado, 0, 1);
                            $estadoXml = strval($docData->email_recibido ?? '');
                            $estadoPago = substr($estado, 2, 1);
                            $factEstado = $estadoSii.$estadoXml.$estadoPago;
                            Factura::where('id', $doc->id)->update(['estado' => $factEstado]);
                        }
                        // Notas de credito
                        $pendientes = NotaCredito::where('estado', 'regexp', '[0-3]0')->get();
                        foreach ($pendientes as $doc) {
                            $docData = $facturapi->consultarEstadoCorreo(61, $doc->folio);
                            $estado = strval($doc->estado);
                            $estadoSii = substr($estado, 0, 1);
                            $estadoXml = strval($docData->email_recibido ?? '');
                            $ncEstado = $estadoSii.$estadoXml;
                            NotaCredito::where('id', $doc->id)->update(['estado' => $ncEstado]);
                        }
                    }catch(Exception $ex){
                        Log::info("Error revisando estado de correo intercambio documentos generados");
                    }
                }))->everyFifteenMinutes();

                /**
                 * Tarea que envía a FacturAPI las cesiones nuevas (estado 0, aún no enviadas).
                 * El envío exitoso NO significa aceptada: solo confirma que FacturAPI recibió y
                 * armó el AEC. El estado real (aceptada/rechazada) lo resuelve el bloque de
                 * polling siguiente contra GET /cesiones/{id}, igual que hacía v2 originalmente
                 * al esperar la revisión del SII antes de marcar 'estado' => 1.
                 */
                $schedule->call($tenant->callback(function() {
                    $facturapi = new FacturapiService();
                    $cesiones = Cesion::where('estado', 0)->whereNull('facturapi_cesion_id')->get();

                    foreach($cesiones as $cesion){
                        $aecs = AEC::where('cesion_id', $cesion->id)->get();
                        if($aecs->isEmpty()){
                            continue;
                        }
                        // v3 acepta ceder varios documentos en un solo call, a diferencia de v2 (uno por request)
                        $documentos = [];
                        foreach($aecs as $aec){
                            $factura = Factura::where('id', $aec->factura_id)->first();
                            if($factura != null){
                                $documentos[] = ['tipo' => 33, 'folio' => $factura->folio];
                            }
                        }
                        $docData = $facturapi->cederDocumentos($documentos, [
                            'rut_factoring' => $cesion->factoring->rut,
                            'razon_social_factoring' => $cesion->factoring->razon_social,
                            'direccion_factoring' => $cesion->factoring->direccion,
                            'email_cesion' => $cesion->factoring->email_cesion,
                            'email' => $cesion->factoring->email_cesion,
                        ]);
                        Log::info("Datos recibidos cesión:");
                        Log::info($docData);
                        if($docData->success ?? false){
                            // @unverified-response-shape: no está confirmado si el id de la cesión creada
                            // viene como 'id' o 'trackid' en la respuesta de POST /documentos/cesionar.
                            $facturapiCesionId = $docData->id ?? ($docData->trackid ?? null);
                            foreach($aecs as $aec){
                                $factura = Factura::where('id', $aec->factura_id)->first();
                                if($factura != null){
                                    $estado = $factura->estado;
                                    $estadoEnvio = substr($estado, 0, 1);
                                    $estadoXML = substr($estado, 1, 1);
                                    $estadoCesion = 1;
                                    $factura->estado = $estadoEnvio.$estadoXML.$estadoCesion;
                                    $factura->save();
                                }
                                $aec->track_id = $facturapiCesionId;
                                $aec->save();
                            }
                            $cesion->facturapi_cesion_id = $facturapiCesionId;
                            // estado se mantiene en 0 (en proceso): el polling siguiente confirma aceptada/rechazada
                        }else{
                            $cesion->estado = 2;
                        }
                        $cesion->save();
                    }
                }))->everyMinute();

                /**
                 * Tarea que consulta el estado real de las cesiones ya enviadas (GET /cesiones/{id}).
                 * @unverified-response-shape: el shape exacto de la respuesta no está confirmado (qué
                 * campo/valores indican aceptada vs rechazada). Se loguea la respuesta cruda de cada
                 * consulta para poder ajustar el mapeo una vez confirmado en el primer test real; si
                 * no se reconoce el estado, la cesión se deja sin tocar (sigue "en proceso").
                 */
                $schedule->call($tenant->callback(function() {
                    $facturapi = new FacturapiService();
                    $cesiones = Cesion::where('estado', 0)->whereNotNull('facturapi_cesion_id')->get();

                    foreach($cesiones as $cesion){
                        $docData = $facturapi->consultarCesion((string) $cesion->facturapi_cesion_id);
                        Log::info("Estado consultado cesión {$cesion->id} (facturapi_cesion_id={$cesion->facturapi_cesion_id}):");
                        Log::info($docData);

                        $estadoTexto = strtolower((string) ($docData->estado ?? ''));
                        $nuevoEstado = null;
                        if(str_contains($estadoTexto, 'rechaz')){
                            $nuevoEstado = 2;
                        }elseif(str_contains($estadoTexto, 'acept')){
                            $nuevoEstado = 1;
                        }

                        if($nuevoEstado === null){
                            continue;
                        }

                        $aecs = AEC::where('cesion_id', $cesion->id)->get();
                        foreach($aecs as $aec){
                            $aec->estado = $nuevoEstado;
                            $aec->save();
                        }
                        $cesion->estado = $nuevoEstado;
                        $cesion->save();
                    }
                }))->everyFiveMinutes();

                /**
                 * Tarea que revisa cada 30 min las facturas de compra recibidas.
                 * FacturAPI refresca su cache contra el SII ~cada 1 hr, así que con 30 min
                 * de intervalo nunca se pierde una corrida entre actualizaciones.
                 */
                $schedule->call($tenant->callback(function() {
                    try{
                        // Mes actual + mes anterior: el SII deja los documentos PENDIENTE de acuse
                        // hasta 8 días, por lo que a inicios de mes siguen llegando del mes previo.
                        $periodos = ComprasUnificadasSyncService::periodosPorDefecto();
                        $emisor = Ajustes::getEmisor();

                        Log::info("[COMPRA] Se inicia revision de facturas en contribuyente ".$emisor['razon_social']);

                        $sync = new ComprasUnificadasSyncService(new FacturapiService());
                        $recienRecibidos = $sync->sincronizar(33, $periodos);

                        $users = User::all();
                        foreach($recienRecibidos as $doc){
                            Notification::sendNow($users, new DocumentoRecibido($doc->rut_emisor, 33, $doc->folio));
                            Log::info("Se envia notificacion a usuarios por doc ". $doc->rut_emisor." - ".$doc->folio);
                        }
                    }catch(Exception $ex){
                        Log::info("Error sincronizado las facturas de compra");
                        Log::error($ex);
                    }
                }))->everyThirtyMinutes()->withoutOverlapping();

                /**
                 * Tarea que revisa cada 30 min las notas de credito de compra recibidas
                 */
                $schedule->call($tenant->callback(function() {
                    try{
                        $periodos = ComprasUnificadasSyncService::periodosPorDefecto();
                        $emisor = Ajustes::getEmisor();
                        Log::info("[COMPRA] Se inicia revision de notas de credito en contribuyente ".$emisor['razon_social']);

                        $sync = new ComprasUnificadasSyncService(new FacturapiService());
                        $recienRecibidos = $sync->sincronizar(61, $periodos);

                        $users = User::all();
                        foreach($recienRecibidos as $doc){
                            Notification::sendNow($users, new DocumentoRecibido($doc->rut_emisor, 61, $doc->folio));
                            Log::info("Se envia notificacion a usuarios por doc ". $doc->rut_emisor." - ".$doc->folio);
                        }
                    }catch(Exception $ex){
                        Log::info("Error sincronizando las notas de credito de compra");
                        Log::error($ex);
                    }
                }))->everyThirtyMinutes()->withoutOverlapping();

                /**
                 * Tarea que envia el reporte de facturas de compra por categorizar todos los dias a las 1/:00
                 */
                $schedule->call($tenant->callback(function() {
                    $emisor = Ajustes::getEmisor();
                    $pendientes = FacturaCompra::whereNull('proyecto_id')->get();

                    // Luego podriamos armar el corrreo con las estadisticas y enviarlo a las listas de correo
                    Log::info("Enviando reporte de la empresa ".$emisor['razon_social']);
                    $correos = ListaCorreo::where('lista', 'reportes')->get();
                    foreach($correos as $correo){
                        Mail::to($correo->direccion)->send(new ReporteDiarioPendientes(($correo->usuario->name.' '.$correo->usuario->lastname), $emisor['razon_social'], count($pendientes)));
                    }

                }))->weekdays()->dailyAt('17:00');
            }
          } catch (Exception $ex) {
            // Un tenant con problemas (credenciales, BD, etc.) no debe impedir que se registren
            // las tareas del resto de los tenants.
            Log::error("Error registrando tareas programadas para el tenant ".($tenant->name ?? '?').": ".$ex->getMessage());
          } finally {
            Tenant::forgetCurrent();
          }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
