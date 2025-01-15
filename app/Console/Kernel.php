<?php

namespace App\Console;

use App\DocumentoPendiente;
use App\Factura;
use App\FacturaCompra;
use App\GuiaDespacho;
use App\Helpers\Ajustes;
use App\NotaCredito;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
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

        // $schedule->command('inspire')
        //          ->hourly();
        Tenant::all()->eachCurrent(function(Tenant $tenant) use ($schedule) {
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
                        if(FacturaCompra::where('rut_emisor', $rut_emisor)->where('folio', $doc->detNroDoc)->count() == 0){
                            FacturaCompra::insertOrIgnore([
                                'rut_emisor' => $rut_emisor,
                                'razon_social_emisor' => $doc->detRznSoc,
                                'folio' => $doc->detNroDoc,
                                'fecha_emision' => date('Y-m-d', strtotime($fecha)),
                                'monto_neto' => $doc->detMntNeto,
                                'monto_iva' => $doc->detMntIVA,
                                'monto_total' => $doc->detMntTotal,
                                'tiene_xml' => false
                            ]);
                        }
                    }
                }
                // Obtener los documentos recibidos en el correo
                $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/compras?contribuyente='.$emisor['rut'].'&tipo=33&periodo='.$periodo;
                Log::info('URL ENDPOINT: '. $endpoint);
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
                Log::info($docData);
                foreach($docData as $doc){
                    if(FacturaCompra::where('rut_emisor', $doc->rut_emisor)->where('folio', $doc->folio)->count() != 0){
                        Log::info('Entro al documento '.$doc->rut_emisor.' - '.$doc->folio.'...');
                        FacturaCompra::where('rut_emisor', $doc->rut_emisor)
                                        ->where('folio', $doc->folio)
                                        ->update(['tiene_xml', true]);
                    }
                }
                // Opcion 1: Hacer un merge de arrays e ingresar masivamente
                // Opcion 2: Insertar todos los docs del RCV y luego hacer un update masivo
                // con los docs recibidos en el correo (tiene_xml = si)
            }))->everyFiveMinutes();
        });

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
