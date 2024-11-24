<?php

namespace App\Http\Controllers;

use App\Helpers\Ajustes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SolucionTotal\CoreDTE\Sii\EnvioDte;
use Yajra\DataTables\Facades\DataTables;

class FacturaCompraController extends Controller
{
    public function index(){
        $emisor = Ajustes::getEmisor();
        $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/compras?contribuyente='.$emisor['rut'].'&tipo=33';
        $ch = curl_init( $endpoint );
            curl_setopt( $ch, CURLOPT_POST, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            $data = json_decode($result);
            curl_close($ch);
            Log::info("ENDPOINT FACTURAS COMPRA: ". $endpoint);

        return view('pages.compras.facturas.index', ['documentos' => $data]);
    }

    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $emisor = Ajustes::getEmisor();
        $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/compras?contribuyente='.$emisor['rut'].'&tipo=33';
        $ch = curl_init( $endpoint );
            curl_setopt( $ch, CURLOPT_POST, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);
            $docData = json_decode($result);
            Log::info("ENDPOINT FACTURAS COMPRA: ". $endpoint);

            return $result;
    }

    public function vistaPreviaFactura(Request $request, $rutEmisor, $tipo, $folio){
        $emisor = Ajustes::getEmisor();
        $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/compras/generar/xml/'.$rutEmisor.'/'.$tipo.'/'.$folio.'?contribuyente='.$emisor['rut'];
        $ch = curl_init( $endpoint );
            curl_setopt( $ch, CURLOPT_POST, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);
            $EnvioDTE = new EnvioDte();
            $EnvioDTE->loadXML($result);
            $dte = $EnvioDTE->getDocumentos()[0];
            $caratula = $EnvioDTE->getCaratula();
            $data = $dte->getDatos();

            $pdf = new \SolucionTotal\CorePDF\PDF($data, 1, '', 2, $dte->getTED());
            $pdf->setCedible(false);
            //$pdf->setLeyendaImpresion('Sistema de facturacion por SoluciónTotal');
            $pdf->setResolucion(date('Y', strtotime($caratula['FchResol'])), $caratula['NroResol']);
            $pdf->construir();
            $pdf->generar(1);
    }



}
