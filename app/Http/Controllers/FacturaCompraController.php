<?php

namespace App\Http\Controllers;

use App\CategoriaDocumento;
use App\FacturaCompra;
use App\Helpers\Ajustes;
use App\PagoFacturaCompra;
use App\Proyecto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
            $data = $dte->getDatos();
        $categorias = CategoriaDocumento::all();
        $fact = FacturaCompra::where('rut_emisor', $rutEmisor)->where('folio', $folio)->first();
        $pagos = PagoFacturaCompra::where('factura_compra_id', $fact->id)->get();
        $proyectos = Proyecto::orderBy('nombre', 'asc')->get();
        return view('pages.compras.facturas.detail', ['documento' => $data, 'factura' => $fact, 'categorias' => $categorias, 'pagos' => $pagos, 'proyectos' => $proyectos]);
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

    public function categorizarFactura(Request $request, $emisor, $folio){
        $validator = Validator::make($request->all(), [
            'categoria' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => 'false',
                'msg' => 'La información ingresada no es suficiente para completar el registro',
                'error' => $validator->errors()
            ]);
        }

        $documento = FacturaCompra::where('rut_emisor', $emisor)->where('folio', $folio)->first();
        $documento->categoria_documento_id = $request->categoria;
        $documento->save();

        return response()->json([
            'success' => true,
            'msg' => 'Documento categorizado exitosamente',
        ]);
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
            if(str_contains($result, '<EnvioDTE')){
                $caratula = $EnvioDTE->getCaratula();
            }else{
                $caratula = [
                    'FchResol' => date('Y'),
                    'NroResol' => 0
                ];
            }
            $dte = $EnvioDTE->getDocumentos()[0];
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

    public function agregarPago(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'tipo_pago' => 'required',
                'fecha_pago' => 'required|date',
                'monto_pago' => 'required',
                'glosa' => '',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $pago = new PagoFacturaCompra();
            $pago->tipo_pago = $request->tipo_pago;
            $pago->fecha_pago = date('Y-m-d', strtotime($request->fecha_pago));
            $pago->monto_pago = $request->monto_pago;
            $pago->factura_compra_id = $id;
            $glosa = $request->glosa;
            if($glosa==null)
                $glosa='';
            $pago->glosa = $glosa;
            $pago->save();
            Log::info("Pago creado hasta aqqui");
            return response()->json([
                'success' => true,
                'msg' => 'Pago agregado exitosamente a la factura de compra'
            ]);

        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un problema al agregar el pago',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function eliminarPago(Request $request, $id){
        try{
            $pago = PagoFacturaCompra::find($id);
            $pago->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Pago eliminado correctamente'
            ]);
        }catch(Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un problema al eliminar el pago',
                'error' => $ex->getMessage()
            ]);
        }
    }

}
