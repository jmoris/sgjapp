<?php

namespace App\Http\Controllers;

use App\CategoriaDocumento;
use App\FacturaCompra;
use App\Helpers\Ajustes;
use App\Notifications\DocumentoRecibido;
use App\OrdenCompra;
use App\PagoFacturaCompra;
use App\Proyecto;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use phpseclib\Crypt\RC2;
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

    public function indexPendientes(Request $request){
            $emisor = Ajustes::getEmisor();
            Log::info("[COMPRA] Revision de documentos pendientes en contribuyente ".$emisor['razon_social']);
            // Obtener RCV de Compra, estos documentos son los recibidos en el SII
            $dataPost = [
                'contribuyente' => $emisor['rut'],
                'operacion' => 'COMPRA',
                'periodo' => date('Ym'),
                'detalle' => 'PENDIENTE',
                'tipo_doc' => 33,
                'clavesii' => 5001
            ];
            $url = env('FACTURAPI_ENDPOINT').'rcv/detalle?'.http_build_query($dataPost);
            $ch = curl_init( $url );
            curl_setopt( $ch, CURLOPT_POST, false);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            Log::info("RESULTADO RCV DE COMPRA: ". $result);
            $response = json_decode($result);
            $data = [];
            if($response != null){
                if($response->data != null){
                    $data = $response->data;
                }
            }
        return view('pages.compras.facturas.pendientes.index', ['emisor' => $emisor, 'documentos' => $data]);
    }

    public function indexReclamadas(Request $request){
        $emisor = Ajustes::getEmisor();
        Log::info("[COMPRA] Revision de documentos pendientes en contribuyente ".$emisor['razon_social']);
        // Obtener RCV de Compra, estos documentos son los recibidos en el SII
        $dataPost = [
            'contribuyente' => $emisor['rut'],
            'operacion' => 'COMPRA',
            'periodo' => date('Ym'),
            'detalle' => 'RECLAMADO',
            'tipo_doc' => 33
        ];
        $url = env('FACTURAPI_ENDPOINT').'rcv/detalle?'.http_build_query($dataPost);
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, false);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Authorization: Bearer '.env('FACTURAPI_TOKEN')
        ]);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        $response = json_decode($result);
        $data = [];
        if($response != null){
            if($response->data != null){
                $data = $response->data;
            }
        }
    return view('pages.compras.facturas.reclamadas.index', ['emisor' => $emisor, 'documentos' => $data]);
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
            $data = FacturaCompra::with('proyecto')->whereRaw('1=1');

            if($request->has('feMinDate') and $request->has('feMaxDate')){
                $data->where('fecha_emision', '>=', $request->feMinDate);
                $data->where('fecha_emision', '<=', date('Y-m-d', strtotime($request->feMaxDate)));
                //$data->whereBetween('fecha_emision', [$request->feMinDate, $request->feMaxDate]);
            }
            if($request->has('proyecto')){
                if($request->proyecto == 1){
                    $data->where('proyecto_id', '!=', null);
                }else if($request->proyecto == 2){
                    $data->where('proyecto_id', null);
                }
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

    public function asignarProyectoFactura(Request $request, $emisor, $folio){
        $validator = Validator::make($request->all(), [
            'proyecto' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => 'false',
                'msg' => 'La información ingresada no es suficiente para completar el registro',
                'error' => $validator->errors()
            ]);
        }

        $documento = FacturaCompra::where('rut_emisor', $emisor)->where('folio', $folio)->first();
        $documento->proyecto_id = $request->proyecto;
        $documento->save();

        return response()->json([
            'success' => true,
            'msg' => 'Proyecto asignado exitosamente',
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

    public function sincronizarDocumentos(Request $request){
        try{
            // Periodo es el mes actual
            $periodo = date('Ym');
            if(isset($request->periodo))
                $periodo = $request->periodo;
            $emisor = Ajustes::getEmisor();

            Log::info("[COMPRA] Se inicia revision de facturas en contribuyente ".$emisor['razon_social']);

            // Obtener RCV de Compra, estos documentos son los recibidos en el SII
            $data = [
                'contribuyente' => $emisor['rut'],
                'operacion' => 'COMPRA',
                'periodo' => $periodo,
                'tipo_doc' => 33,
                'clavesii' => 5001
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
            if($response != null){
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
            if($result != null){
                $docData = json_decode($result);

                foreach($docData as $data){
                    $doc = FacturaCompra::where('rut_emisor', $data->rut_emisor)->where('folio', $data->folio)->where('tiene_xml', false)->first();
                    if($doc != null){
                        $users = User::all();
                        $doc->oc_id = $data->oc_id;
                        $ocdoc = OrdenCompra::where('folio', $data->oc_id)->first();
                        if($ocdoc != null){
                            $doc->proyecto_id = $ocdoc->proyecto_id;
                        }
                        $doc->fecha_vencimiento = date('Y-m-d', strtotime($data->fecha_vencimiento));
                        $doc->tiene_xml = true;
                        $doc->save();
                        Notification::sendNow($users, new DocumentoRecibido($data->rut_emisor, 33, $data->folio));
                        Log::info("Se envia notificacion a usuarios por doc ". $data->rut_emisor." - ".$data->folio);
                    }
                }
            }
            return response()->json([
                'success' => true,
                'msg' => 'Los documentos fueron sincronizados correctamente',
                'timestamp' => now(),
            ]);
        }catch(Exception $ex){
            Log::info("Error sincronizado las facturas de compra");
            Log::error($ex);
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un error intentando sincronizar los documentos con la API',
                'timestamp' => now(),
            ]);
        }
    }

    public function agregarEventoDTE(Request $request){
        try{
            $url = env('FACTURAPI_ENDPOINT').'rcv/agregarevento';
            $emisor = Ajustes::getEmisor();
            $dataPost = [
                'contribuyente' => $emisor['rut'],
                'rut_receptor' => $request->rut,
                'tipo' => '33',
                'folio' => $request->folio,
                'evento' => $request->evento
            ];
            $ch = curl_init($url );
            curl_setopt( $ch, CURLOPT_POST, true);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($dataPost) );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            Log::info($result);
            curl_close($ch);
            return $result;
        }catch(Exception $ex){
            Log::info($ex);
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un error al agregar el evento al DTE',
                'error' => $ex->getMessage()
            ]);
        }
    }

}
