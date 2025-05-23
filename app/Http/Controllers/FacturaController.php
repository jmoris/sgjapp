<?php

namespace App\Http\Controllers;

use App\Borrador;
use App\CategoriaDocumento;
use App\Cliente;
use App\Comuna;
use App\DocumentoPendiente;
use App\Factura;
use App\Helpers\Ajustes;
use App\Helpers\Herramientas;
use App\LineaFactura;
use App\ListaPrecio;
use App\PagoFactura;
use App\PagoFacturaCompra;
use App\Proyecto;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use SolucionTotal\CoreDTE\Sii\EnvioDte;
use Yajra\DataTables\Facades\DataTables;

class FacturaController extends Controller
{
    public function index(){
        return view('pages.ventas.facturas.index');
    }

    public function show($folio){
        $emisor = Ajustes::getEmisor();
        $endpoint =  env('FACTURAPI_ENDPOINT').'documentos/generar/xml/33/'.$folio.'?contribuyente='.$emisor['rut'];
        Log::info($endpoint);
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
                return response()->back();
            }
            $EnvioDTE = new EnvioDte();
            $EnvioDTE->loadXML($result);
            $dte = $EnvioDTE->getDocumentos()[0];
            $data = $dte->getDatos();
        $categorias = CategoriaDocumento::all();
        $fact = Factura::where('folio', $folio)->first();
        $pagos = PagoFactura::where('factura_id', $fact->id)->get();
        $proyectos = Proyecto::all();
        return view('pages.ventas.facturas.detail', ['documento' => $data, 'factura' => $fact, 'categorias' => $categorias, 'pagos' => $pagos, 'proyectos' => $proyectos]);
    }

    public function newFactura(){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::orderBy('nombre', 'asc')->get();
        $clientes = Cliente::orderBy('razon_social', 'asc')->get(); // aqui clientes
        $unidades = Unidad::orderBy('nombre', 'asc')->get();
        $listas = ListaPrecio::all();
        $proyectos = Proyecto::where('estado', '!=', 1)->with('proyectopadre')->orderBy('nombre', 'asc')->get();
        $borradores = Borrador::where('user_id', auth()->user()->id)->where('tipo_doc', 33)->orderBy('updated_at', 'desc')->get();
        return view('pages.ventas.facturas.create', ['borradores'=>$borradores, 'clientes' => $clientes, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'listas' => $listas, 'proyectos' => $proyectos]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(Request $request){
        $data = Factura::with('cliente');

        if($request->has('feMinDate') and $request->has('feMaxDate')){
            $data->where('fecha_emision', '>=', $request->feMinDate);
            $data->where('fecha_emision', '<=', date('Y-m-d', strtotime($request->feMaxDate.' +1 days')));

            //$data->whereBetween('fecha_emision', [$request->feMinDate, $request->feMaxDate]);
        }

        if($request->has('fvMinDate') and $request->has('fvMaxDate')){
            $data->whereBetween('fecha_vencimiento', [$request->fvMinDate, $request->fvMaxDate]);
        }

        //$data = Documento::where('documento.tipo', 52)->where('ref_contribuyente', \Auth::user()->ref_contribuyente);
        return DataTables::of($data)
            ->addIndexColumn()
            ->filterColumn('folio', function($query, $keyword) {
                $folios = explode(',', str_replace(' ', '', $keyword));
                $query->whereIn('folio', $folios);
            })
            ->make(true);
        //$data = Factura::with('cliente');
        //return DataTables::eloquent($data)->toJson();
    }

    public function vistaPreviaEnvioFactura(Request $request){
        set_time_limit(300);
        try{
            $validator = Validator::make($request->all(), [
                'fecha_emision' => 'required',
                'cliente' => 'required',
                'tipo_pago' => 'required',
                'fecha_vencimiento' => 'required',
                'items' => 'required|array',
                'referencias' => 'nullable|array',
                'glosa' => 'nullable',
                'proyecto' => 'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $emisor = Ajustes::getEmisor();
            $cliente = Cliente::where('id', $request->cliente)->with('comuna')->first();

            $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');
            $detalle = [];
            $neto = 0;
            foreach($request->items as $item){
                array_push($detalle, [
                    'NmbItem' => mb_convert_encoding($item['nombre'], 'UTF-8', 'ISO-8859-1'),
                    'DscItem' => ((!array_key_exists('descripcion', $item))?false:Herramientas::sanitizarString($item['descripcion'])),
                    'UnmdItem' => Unidad::find($item['unidad'])->abreviacion,
                    'PrcItem' => $item['precio'],
                    'QtyItem' => $item['cantidad'],
                    'MontoItem' => $item['precio'] * $item['cantidad'],
                ]);
                $neto += $item['precio'] * $item['cantidad'];
            }
            $detalle = array_filter($detalle);

            $refArray = ($request->referencias==null)?[]:$request->referencias;


            $referencias = [];
            $linea = 1;
            foreach($refArray as $ref){
                array_push($referencias, [
                    'NroLinRef' => $linea,
                    'TpoDocRef' => $ref['tipo'],
                    'FolioRef' => $ref['folio'],
                    'FchRef' => $ref['fecha'],
                    'RazonRef' => ' ',
                    'CodRef' => false
                ]);
                $linea++;
            }
            $vencimiento = date('Y-m-d', strtotime(str_replace('/', '-', $request->fecha_vencimiento)));

            $dte = [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 33,
                        'Folio' => 'SIN FOLIO',
                        'FchEmis' => $str,
                        'FchVenc' => $vencimiento,
                        'FmaPago' => $request->tipo_pago,
                    ],
                    'Emisor' => [
                        'RUTEmisor' => $emisor['rut'],
                        'RznSoc' => $emisor['razon_social'],
                        'GiroEmis' => $emisor['giro'],
                        'Acteco' => 251100,
                        'DirOrigen' => $emisor['direccion'],
                        'CmnaOrigen' => mb_convert_encoding(Comuna::find($emisor['comuna'])->nombre, 'UTF-8', 'ISO-8859-1'),
                    ],
                    'Receptor' => [
                        'RUTRecep' => $cliente->rut,
                        'RznSocRecep' => mb_convert_encoding($cliente->razon_social, 'UTF-8', 'ISO-8859-1'),
                        'GiroRecep' =>  mb_convert_encoding($cliente->giro, 'UTF-8', 'ISO-8859-1'),
                        'DirRecep' =>  mb_convert_encoding($cliente->direccion, 'UTF-8', 'ISO-8859-1'),
                        'CmnaRecep' =>  mb_convert_encoding($cliente->comuna->nombre, 'UTF-8', 'ISO-8859-1'),
                        'CdgIntRecep' => 'CASA MATRIZ'
                    ],
                    'Totales' => [
                        'MntNeto' => $neto,
                        'IVA' => $neto * 0.19,
                        'MntTotal' => $neto + ($neto * 0.19),
                    ]
                ],
                'Detalle' => $detalle,
                'Referencia' => $referencias
            ];
            $pdf = new \SolucionTotal\CorePDF\PDF($dte, 1, 'https://intranet.joremet.cl/logo_joremet.png', 2);
            $pdf->setCedible(false);
            //$pdf->setLeyendaImpresion('Sistema de facturacion por SoluciónTotal');
            $pdf->setTelefono($emisor['telefono']);
            $pdf->setWeb($emisor['web']);
            $pdf->setMail($emisor['email']);
            $pdf->setMarcaAgua('https://intranet.joremet.cl/logo_joremet.png');
            $glosa = str_replace('//', '<br>', $request->glosa);
            $pdf->setGlosa($glosa);
            $proyecto = Proyecto::find($request->proyecto);
            $pdf->setObra($proyecto->nombre);
            $pdf->setCedible(false);
            //$pdf->setFirmaIzquierda($oc->usuario->cargo, "<img style='height: 80px;' src='https://i.postimg.cc/j29cg3BZ/Jesus-Moris.png'>");
            $pdf->construir();
            $generado = base64_encode($pdf->generar(3)."?".time());
            return response()->json([
                'PDF' => $generado
            ]);;
        }catch(Exception $ex){
            Log::error($ex);
            return $ex;
        }
    }

    public function categorizarFactura(Request $request, $folio){
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

        $documento = Factura::where('folio', $folio)->first();
        $documento->categoria_documento_id = $request->categoria;
        $documento->save();

        return response()->json([
            'success' => true,
            'msg' => 'Documento categorizado exitosamente',
        ]);
    }

    public function asignarProyectoFacatura(Request $request, $folio){
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

        $documento = Factura::where('folio', $folio)->first();
        $documento->proyecto_id = $request->proyecto;
        $documento->save();

        return response()->json([
            'success' => true,
            'msg' => 'Proyecto asignado exitosamente',
        ]);
    }

    public function storeFactura(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'fecha_emision' => 'required',
                'cliente' => 'required',
                'tipo_pago' => 'required',
                'fecha_vencimiento' => 'required',
                'items' => 'required|array',
                'referencias' => 'nullable|array',
                'glosa' => 'nullable'
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $emisor = Ajustes::getEmisor();
            $cliente = Cliente::where('id', $request->cliente)->with('comuna')->first();


            $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');
            $detalle = [];
            foreach($request->items as $item){
                array_push($detalle, [
                    'nombre' => $item['nombre'],
                    'descripcion' => ((!array_key_exists('descripcion', $item))?false:Herramientas::sanitizarString($item['descripcion'])),
                    'unidad' => Unidad::find($item['unidad'])->abreviacion,
                    'precio' => $item['precio'],
                    'cantidad' => $item['cantidad']
                ]);
            }
            $detalle = array_filter($detalle);

            $refArray = ($request->referencias==null)?[]:$request->referencias;


            $referencias = [];
            foreach($refArray as $ref){
                array_push($referencias, [
                    'tipo' => $ref['tipo'],
                    'folio' => $ref['folio'],
                    'fecha' => $ref['fecha'],
                    'razon' => ' ',
                    'codigo' => false
                ]);
            }
            $vencimiento = date('Y-m-d', strtotime(str_replace('/', '-', $request->fecha_vencimiento)));
            $data = [
                'contribuyente' => $emisor['rut'],
                'acteco' => $emisor['acteco'],
                'tipo' => 33,
                'fecha' => $str,
                'fecha_vencimiento' => $vencimiento,
                'receptor' => [
                    'rut'=> $cliente->rut,
                    'razon_social'=> $cliente->razon_social,
                    'giro'=> $cliente->giro,
                    'direccion'=> $cliente->direccion,
                    'comuna'=> Herramientas::sanitizarString($cliente->comuna->nombre),
                ],
                'tipo_pago' => $request->tipo_pago,
                'detalles' => $detalle,
                'referencias' => $referencias,
                'correo_dte' => $cliente->email_dte
            ];

            Log::info("Datos enviados Factura:");
            Log::info($data);

            $ch = curl_init( env('FACTURAPI_ENDPOINT').'documentos' );
            curl_setopt( $ch, CURLOPT_POST, true);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            Log::info("Datos recibidos Factura:");
            Log::info($result);
            curl_close($ch);
            $docData = json_decode($result);
            if(!$docData->success){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'No se pudo generar el documento en la API',
                    'error' => $docData->error
                ]);
            }

            $fact = new Factura();
            $fact->folio = $docData->folio;
            Log::info($str);
            $fact->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $fact->fecha_vencimiento = $vencimiento;
            $fact->cliente_id = $request->cliente;
            $fact->user_id = auth()->user()->id;
            $fact->tipo_pago = $request->tipo_pago;
            $fact->tipo_descuento = 0;
            $fact->descuento = 0;
            $fact->estado = '000';
            $fact->track_id = $docData->trackid;
            $fact->monto_neto = $docData->totales->neto;
            $fact->monto_iva = $docData->totales->iva;
            $fact->monto_total = $docData->totales->total;
            $fact->proyecto_id = $request->proyecto;
            if(isset($request->glosa)){
                $fact->glosa = str_replace('///', '<br>', $request->glosa);
            }
            $fact->tipo_pago = $request->tipo_pago;
            $fact->save();
            // Se recorre listado de productos OC y se almacenan
            $subtotal = 0;
            foreach($request->items as $item){
                $linea = new LineaFactura();
                $linea->sku = $item['sku'];
                $linea->nombre = $item['nombre'];
                $linea->descripcion = ((!array_key_exists('descripcion', $item))?'':$item['descripcion']);
                $linea->cantidad = $item['cantidad'];
                $linea->unidad = Unidad::find($item['unidad'])->abreviacion;
                $linea->precio_unitario = $item['precio'];
                $linea->descuento = ((!array_key_exists('descuento', $item))?0:$item['descuento']);
                $linea->factura_id = $fact->id;
                $linea->save();
            }

            $pendiente = new DocumentoPendiente();
            $pendiente->tipo_doc = 33;
            $pendiente->folio = $fact->folio;
            $pendiente->track_id = $docData->trackid;
            $pendiente->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'result' => $result
            ]);
        }catch(Exception $ex){
            Log::error('Usuario conectado: '.auth()->user());
            Log::error($ex);
            return $ex;
        }
    }

    public function vistaPreviaFactura(Request $request, $folio){
        try{
            set_time_limit(300);
            $emisor = Ajustes::getEmisor();
            $fact = Factura::with('cliente', 'cliente.comuna')->where('folio', $folio)->first();
            $endpoint = env('FACTURAPI_ENDPOINT').'documentos/generar/xml/33/'.$folio.'?contribuyente='.$emisor['rut'];
            Log::info("Endpoint Vista Previa : ". $endpoint);
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
                return response()->back();
            }

            $EnvioDTE = new EnvioDte();
            $EnvioDTE->loadXML($result);
            $dte = $EnvioDTE->getDocumentos()[0];
            $caratula = $EnvioDTE->getCaratula();
            $data = $dte->getDatos();

            $pdf = new \SolucionTotal\CorePDF\PDF($data, 1, 'https://intranet.joremet.cl/logo_joremet.png', 2, $dte->getTED());
            $pdf->setCedible(false);
            //$pdf->setLeyendaImpresion('Sistema de facturacion por SoluciónTotal');
            $pdf->setObra($fact->proyecto->nombre);
            $pdf->setTelefono($emisor['telefono']);
            $pdf->setResolucion(date('Y', strtotime($caratula['FchResol'])), $caratula['NroResol']);
            $pdf->setWeb($emisor['web']);
            $pdf->setMail($emisor['email']);
            $pdf->setMarcaAgua('https://intranet.joremet.cl/logo_joremet.png');
            $glosa = str_replace('//', '<br>', $fact->glosa);
            $pdf->setGlosa($glosa);
            $pdf->construir();
            if($request->descargar==1){
                $pdf->generar(0);
            }else{
                $pdf->generar(1);
            }
        }catch(Exception $ex){
            Log::error($ex);
            return response()->json([
                'status' => 500,
                'msg' => 'No se pudo generar la vista previa del documento'
            ], 500);
        }
    }

    public function descargarXML(Request $request, $folio){
        try{
            $emisor = Ajustes::getEmisor();
            $ch = curl_init( env('FACTURAPI_ENDPOINT').'documentos/generar/xml/33/'.$folio.'?contribuyente='.$emisor['rut']);
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
                'Content-Disposition' => 'attachment; filename="DTET33F'. $folio .'.xml"',
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

            $pago = new PagoFactura();
            $pago->tipo_pago = $request->tipo_pago;
            $pago->fecha_pago = date('Y-m-d', strtotime($request->fecha_pago));
            $pago->monto_pago = $request->monto_pago;
            $pago->factura_id = $id;
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
            $pago = PagoFactura::find($id);
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
