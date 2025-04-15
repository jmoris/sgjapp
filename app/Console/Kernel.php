<?php

namespace App\Console;

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
use App\OrdenCompra;
use App\Permiso;
use App\User;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Spatie\Multitenancy\Models\Tenant;

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
        $tenants = Tenant::all();
        try{
            foreach($tenants as $tenant){
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

                }))->dailyAt('17:00');

                /**
                 * Tarea que revisa cada 15 min las facturas de compra recibidas
                 */
                $schedule->call($tenant->callback(function(){
                    // Periodo es el mes actual
                    $periodo = date('Ym');
                    $emisor = Ajustes::getEmisor();
                    // Obtener RCV de Compra, estos documentos son los recibidos en el SII
                    $data = [
                        'contribuyente' => $emisor['rut'],
                        'operacion' => 'COMPRA',
                        'periodo' => $periodo,
                        'tipo_doc' => 33
                    ];
                    $url = env('FACTURAPI_ENDPOINT').'rcv/detalle?'.http_build_query($data);
                    $ch = curl_init( $url );
                    curl_setopt( $ch, CURLOPT_POST, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                        'Content-Type:application/json',
                        'Authorization: Bearer '.env('FACTURAPI_TOKEN')
                    ]);
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    if($response == null){
                        return 0;
                    }
                    if($response->data != null){
                        foreach($response->data as $doc){
                            $fecha = str_replace('/', '-', $doc->detFchDoc);
                            $rut_emisor = $doc->detRutDoc.'-'.$doc->detDvDoc;
                            if(FacturaCompra::where('rut_emisor', $rut_emisor)->where('folio', intval($doc->detNroDoc))->count() == 0){

                                $factura = new FacturaCompra();
                                $factura->rut_emisor = $rut_emisor;
                                $factura->razon_social_emisor = $doc->detRznSoc;
                                $factura->folio = $doc->detNroDoc;
                                $factura->fecha_emision = date('Y-m-d', strtotime($fecha));
                                $factura->monto_neto = $doc->detMntNeto;
                                $factura->monto_iva = $doc->detMntIVA;
                                $factura->monto_total = $doc->detMntTotal;
                                $factura->tiene_xml = false;
                                $factura->save();

                            }
                        }
                    }
                    // Obtener los documentos recibidos en el correo
                    $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/compras?contribuyente='.$emisor['rut'].'&tipo=33&periodo='.$periodo;
                    $ch = curl_init( $endpoint );
                    curl_setopt( $ch, CURLOPT_POST, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                        'Content-Type:application/json',
                        'Authorization: Bearer '.env('FACTURAPI_TOKEN')
                    ]);
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    $result = curl_exec($ch);
                    curl_close($ch);
                    if($result == null){
                        return 0;
                    }
                    $docData = json_decode($result);

                    foreach($docData as $data){
                        $doc = FacturaCompra::where('rut_emisor', $data->rut_emisor)->where('folio', $data->folio)->where('tiene_xml', false)->first();
                        if($doc != null){
                            Log::info(json_encode($data));
                            $users = User::all();
                            $doc->oc_id = $data->oc_id;
                            $ocdoc = OrdenCompra::where('folio', $data->oc_id)->first();
                            if($ocdoc != null){
                                $doc->proyecto_id = $ocdoc->proyecto_id;
                            }
                            $doc->fecha_vencimiento = date('Y-m-d', strtotime($data->fecha_vencimiento));
                            $doc->tiene_xml = true;
                            $doc->save();
                            try{
                                Notification::sendNow($users, new DocumentoRecibido($data->rut_emisor, 33, $data->folio));
                                Log::info("Se envia notificacion a usuarios por doc ". $data->rut_emisor." - ".$data->folio);
                            }catch(Exception $ex){
                                Log::error('Hubo un error al intentar enviar la notificacion del contriuyente '.$data->rut_emisor. ' folio '.$data->folio);
                            }
                        }
                    }
                    // Opcion 1: Hacer un merge de arrays e ingresar masivamente
                    // Opcion 2: Insertar todos los docs del RCV y luego hacer un update masivo
                    // con los docs recibidos en el correo (tiene_xml = si)
                }))->everyFifteenMinutes();

                /**
                 * Tarea que revisa cada 15 min las notas de credito de compra recibidas
                 */
                $schedule->call($tenant->callback(function(){
                    // Periodo es el mes actual
                    $periodo = date('Ym');
                    $emisor = Ajustes::getEmisor();
                    // Obtener RCV de Compra, estos documentos son los recibidos en el SII
                    $data = [
                        'contribuyente' => $emisor['rut'],
                        'operacion' => 'COMPRA',
                        'periodo' => $periodo,
                        'tipo_doc' => 61
                    ];
                    $url = env('FACTURAPI_ENDPOINT').'rcv/detalle?'.http_build_query($data);
                    $ch = curl_init( $url );
                    curl_setopt( $ch, CURLOPT_POST, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                        'Content-Type:application/json',
                        'Authorization: Bearer '.env('FACTURAPI_TOKEN')
                    ]);
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    $result = curl_exec($ch);
                    $response = json_decode($result);
                    if($response == null){
                        return 0;
                    }
                    if($response->data != null){
                        foreach($response->data as $doc){
                            $fecha = str_replace('/', '-', $doc->detFchDoc);
                            $rut_emisor = $doc->detRutDoc.'-'.$doc->detDvDoc;
                            if(NotaCreditoCompra::where('rut_emisor', $rut_emisor)->where('folio', intval($doc->detNroDoc))->count() == 0){

                                $factura = new NotaCreditoCompra();
                                $factura->rut_emisor = $rut_emisor;
                                $factura->razon_social_emisor = $doc->detRznSoc;
                                $factura->folio = $doc->detNroDoc;
                                $factura->fecha_emision = date('Y-m-d', strtotime($fecha));
                                $factura->monto_neto = $doc->detMntNeto;
                                $factura->monto_iva = $doc->detMntIVA;
                                $factura->monto_total = $doc->detMntTotal;
                                $factura->tiene_xml = false;
                                $factura->save();

                            }
                        }
                    }
                    // Obtener los documentos recibidos en el correo
                    $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/compras?contribuyente='.$emisor['rut'].'&tipo=61&periodo='.$periodo;
                    $ch = curl_init( $endpoint );
                    curl_setopt( $ch, CURLOPT_POST, false);
                    curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                        'Content-Type:application/json',
                        'Authorization: Bearer '.env('FACTURAPI_TOKEN')
                    ]);
                    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                    $result = curl_exec($ch);
                    curl_close($ch);
                    if($result == null){
                        return 0;
                    }
                    $docData = json_decode($result);

                    foreach($docData as $data){
                        $doc = NotaCreditoCompra::where('rut_emisor', $data->rut_emisor)->where('folio', $data->folio)->where('tiene_xml', false)->first();
                        if($doc != null){
                            $users = User::all();

                            $doc->fecha_vencimiento = date('Y-m-d', strtotime($data->fecha_vencimiento));
                            $doc->tiene_xml = true;
                            $doc->save();
                            try{
                                Notification::sendNow($users, new DocumentoRecibido($data->rut_emisor, 61, $data->folio));
                                Log::info("Se envia notificacion a usuarios por doc ". $data->rut_emisor." - ".$data->folio);
                            }catch(Exception $ex){
                                Log::error('Hubo un error al intentar enviar la notificacion del contriuyente '.$data->rut_emisor. ' folio '.$data->folio);
                            }
                        }
                    }
                    // Opcion 1: Hacer un merge de arrays e ingresar masivamente
                    // Opcion 2: Insertar todos los docs del RCV y luego hacer un update masivo
                    // con los docs recibidos en el correo (tiene_xml = si)
                }))->everyFifteenMinutes();

                /**
                 * Tarea que revisa el estado de los documentos pendientes
                 */
                $schedule->call($tenant->callback(function() {
                    $emisor = Ajustes::getEmisor();
                    $pendientes = DocumentoPendiente::all();
                    foreach ($pendientes as $doc) {
                        $ch = curl_init(env('FACTURAPI_ENDPOINT').'documentos/consulta?contribuyente=' . $emisor['rut'] . '&trackid=' . $doc->track_id);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type:application/json',
                            'Authorization: Bearer '.env('FACTURAPI_TOKEN')
                        ]);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $result = curl_exec($ch);
                        curl_close($ch);

                        $docData = json_decode($result);
                        if($docData->estadistica != null){
                            $estado = '0';
                            if($docData->estadistica[0]->aceptados == 1||$docData->estadistica[0]->reparos == 1){
                                // documentoa ceptado
                                $estado = '1';
                            }else if($docData->estadistica[0]->rechazados == 1){
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
                                GuiaDespacho::where('folio', $doc->folio)->update([
                                    'track_id' => $doc->track_id,
                                    'estado' => $estado
                                ]);
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
                }))->everyMinute();

                /**
                 * Tarea que revisa el estado del correo de los documentos generados
                 */
                $schedule->call($tenant->callback(function() {
                    try{
                        $emisor = Ajustes::getEmisor();
                        // Facturas
                        $pendientes = Factura::where('estado', 'regexp', '[0-3]0[0|1]')->get();
                        foreach ($pendientes as $doc) {
                            $endpoint = env('FACTURAPI_ENDPOINT').'documentos/33/'.$doc->folio.'?contribuyente='. $emisor['rut'];
                            $ch = curl_init($endpoint);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Content-Type:application/json',
                                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
                            ]);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $result = curl_exec($ch);
                            curl_close($ch);
                            $docData = json_decode($result, true);
                            $estado = strval($doc->estado);
                            $estadoSii = substr($estado, 0, 1);
                            $estadoXml = strval($docData['email_recibido']);
                            $estadoPago = substr($estado, 2, 1);
                            $factEstado = $estadoSii.$estadoXml.$estadoPago;
                            Factura::where('id', $doc->id)->update(['estado' => $factEstado]);
                        }
                        // Notas de credito
                        $pendientes = NotaCredito::where('estado', 'regexp', '[0-3]0')->get();
                        foreach ($pendientes as $doc) {
                            $endpoint = env('FACTURAPI_ENDPOINT').'documentos/61/'.$doc->folio.'?contribuyente='. $emisor['rut'];
                            $ch = curl_init($endpoint);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Content-Type:application/json',
                                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
                            ]);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $result = curl_exec($ch);
                            curl_close($ch);
                            $docData = json_decode($result, true);
                            $estado = strval($doc->estado);
                            $estadoSii = substr($estado, 0, 1);
                            $estadoXml = strval($docData['email_recibido']);
                            $ncEstado = $estadoSii.$estadoXml;
                            NotaCredito::where('id', $doc->id)->update(['estado' => $ncEstado]);
                        }
                    }catch(Exception $ex){
                        Log::error($ex);
                    }
                }))->everyMinute();
            }
        }catch(Exception $ex){
            Log::info("Hubo un error al ejecutar las tareas programadas");
            Log::info("ERROR: ". $ex->getMessage());
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
