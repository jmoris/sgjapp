<?php

namespace App\Console\Commands;

use App\Cliente;
use App\Factura;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cookie;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Spatie\Multitenancy\Models\Tenant;

class ImportarDocumentos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:importar-documentos {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $xmlPath = $this->argument('path');
        $contents = file_get_contents($xmlPath);
        $envio = new \SolucionTotal\CoreDTE\Sii\EnvioDte();
        $envio->loadXML($contents);
        $DTEs = $envio->getDocumentos();
        foreach($DTEs as $dte){
            $data = $dte->getDatos();
            $pdf = new \SolucionTotal\CorePDF\PDF($data, 1, '', 2, $dte->getTED());
            $pdf->setCedible(false);
            $pdf->construir();
            $pdf->generar(2, '/Users/jesusmoris/XMLJoremet/DTET33C'.$data['Encabezado']['Emisor']['RUTEmisor'].'F'.$data['Encabezado']['IdDoc']['Folio'].'.pdf');
        }
    }
}
