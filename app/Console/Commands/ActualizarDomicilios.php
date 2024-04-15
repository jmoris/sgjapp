<?php

namespace App\Console\Commands;

use App\DomicilioContribuyente;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class ActualizarDomicilios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:actualizar-domicilios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para descargar el archivo de domicilios desde el SII y almacenar en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        // Se descarga el archcivo y se almacena en la variable data
        $ch = curl_init();
        $source = "https://www.sii.cl/estadisticas/nominas/PUB_NOM_DIRECCIONES.zip";
        curl_setopt($ch, CURLOPT_URL, $source);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec ($ch);
        curl_close ($ch);

        // Guardar el stream de datos en el path destino
        $destination = storage_path('app/direccion_pj.zip');
        $file = fopen($destination, "w+");
        fputs($file, $data);
        fclose($file);

        Log::info("Informacion de Domicilios descargada");

        // Desempaquetamos el archivo
        $zip = new ZipArchive;
        $res = $zip->open($destination);
        if ($res === TRUE) {
            $zip->extractTo(storage_path('app/')); // directory to extract contents to
            $zip->close();
            unlink($destination);
            Log::info("Informacion de Domicilios descomprimida y liberada");
        } else {
            Log::error("Fallo la descompresion del archivo de domicilios");
            return 0;
        }
        // Se eliminan todas los registros que existen en la tabla
        DomicilioContribuyente::truncate();
        $row = 1;
        $path = storage_path('app/PUB_NOM_DOMICILIO.txt');
        $lines = file($path);
        $count = 0;
        foreach($lines as $line) {
            $count += 1;
            $newLine = str_replace("\"", "", $line);
            $data = str_getcsv($newLine, "\t");

            if($data[2] == 'S'){
                $domicilio = new DomicilioContribuyente();
                $domicilio->rut = $data[0].'-'.$data[1];
                $domicilio->direccion = addslashes($data[5].($data[6]!=''?' '.$data[6]:'').($data[7]!=''?' '.$data[7]:'').($data[8]!=''?' DPTO '.$data[8]:'').($data[9]!=''?' '.$data[9]:''));
                $domicilio->comuna = $data[11];
                $domicilio->region = $data[12];
                $domicilio->save();
                $row++;
            }
        }
        Log::info("Informacion de Domicilios completada y almaceada. Registros totales: $row");
    }
}
