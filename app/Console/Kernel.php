<?php

namespace App\Console;

use App\DocumentoPendiente;
use App\Factura;
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
                    $ch = curl_init('https://dev.facturapi.cl/api/documentos/consulta?contribuyente=' . $emisor['rut'] . '&trackid=' . $doc->track_id);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type:application/json',
                        'Authorization: Bearer 2|cFBrnEmGhb6eFM9pCUNv55WAWUSV7TcAegK2pAHZda9482c3'
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
