<?php

namespace App\Http\Controllers;

use App\FacturaCompra;
use App\Helpers\Ajustes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SolucionTotal\CoreDTE\Sii\EnvioDte;
use Yajra\DataTables\Facades\DataTables;

class FacturaCompraController extends Controller
{
    public function index(Request $request){
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
            if(isset($data->success)){
                $data = [];
            }
            curl_close($ch);
            Log::info("ENDPOINT FACTURAS COMPRA: ". $endpoint);

        return view('pages.compras.facturas.index', ['documentos' => $data]);
    }

    public function show($rutEmisor, $folio){
        $emisor = Ajustes::getEmisor();
        $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/compras/generar/xml/'.$rutEmisor.'/33/'.$folio.'?contribuyente='.$emisor['rut'];
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

            return view('pages.compras.facturas.detail', ['documento' => $data]);
    }

    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(Request $request){
        /*$emisor = Ajustes::getEmisor();
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

            return $result;*/
            $data = FacturaCompra::whereRaw('1=1');

            if($request->has('feMinDate') and $request->has('feMaxDate')){
                $data->where('fecha_emision', '>=', $request->feMinDate);
                $data->where('fecha_emision', '<=', date('Y-m-d', strtotime($request->feMaxDate)));
                //$data->whereBetween('fecha_emision', [$request->feMinDate, $request->feMaxDate]);
            }
            return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
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

            $pdf = new \SolucionTotal\CorePDF\PDF($data, 1, url('/vacio.png'), 2, $dte->getTED());
            //$pdf->setLeyendaImpresion('Sistema de facturacion por SoluciónTotal');
            $pdf->setResolucion(date('Y', strtotime($caratula['FchResol'])), $caratula['NroResol']);
            $pdf->construir();
            if($request->descargar==1){
                $pdf->generar(0);
            }else{
                $pdf->generar(1);
            }
        }

    public function descargarPDF(Request $request, $emisor, $folio){
        try{
            $emisor = Ajustes::getEmisor();
            $ch = curl_init( env('FACTURAPI_ENDPOINT').'documentos/compras/generar/pdf/'.$emisor.'/33/'.$folio.'?visor=2&contribuyente='.$emisor['rut']);
            curl_setopt( $ch, CURLOPT_POST, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);

            $headers = [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="DTET33F'. $folio .'.pdf"',
                'Content-Transfer-Encoding' => 'binary'
            ];

            return response()->make($result, 200, $headers);
        }catch(Exception $ex){
            return $ex;
        }
    }

}
