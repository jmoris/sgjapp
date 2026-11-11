<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Comuna;
use App\DocumentoPendiente;
use App\Factura;
use App\Helpers\Ajustes;
use App\LineaNC;
use App\Helpers\Herramientas;
use App\Helpers\NotaCreditoRef;
use App\ListaPrecio;
use App\NotaCredito;
use App\Proyecto;
use App\Services\FacturapiService;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use SolucionTotal\CoreDTE\Sii\EnvioDte;
use Yajra\DataTables\Facades\DataTables;

class NotaCreditoController extends Controller
{
    protected FacturapiService $facturapi;

    public function __construct(FacturapiService $facturapi)
    {
        $this->facturapi = $facturapi;
    }

    public function index(){
        return view('pages.ventas.notascredito.index');
    }

    public function newNotaCredito(Request $request){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::orderBy('nombre', 'asc')->get();
        $clientes = Cliente::orderBy('razon_social', 'asc')->get();
        $unidades = Unidad::orderBy('nombre', 'asc')->get();
        $listas = ListaPrecio::all();
        $proyectos = Proyecto::where('estado', '!=', 1)->with('proyectopadre')->orderBy('nombre', 'asc')->get();

        // Prefill opcional desde una factura existente (?factura=ID)
        $prefill = null;
        if($request->filled('factura')){
            $fact = Factura::with('lineas')->find($request->factura);
            if($fact){
                $prefill = [
                    'cliente' => $fact->cliente_id,
                    'proyecto' => $fact->proyecto_id,
                    'tipo_pago' => $fact->tipo_pago,
                    'items' => $fact->lineas->map(function($l){
                        return [
                            'sku' => $l->sku ?? '',
                            'nombre' => $l->nombre,
                            'descripcion' => $l->descripcion ?? '',
                            'unidad' => optional(Unidad::where('abreviacion', $l->unidad)->first())->id,
                            'precio' => (float) $l->precio_unitario,
                            'cantidad' => (float) $l->cantidad,
                        ];
                    })->values(),
                    'referencia' => [
                        'tipo' => 33,
                        'folio' => $fact->folio,
                        'fecha' => date('Y-m-d', strtotime($fact->fecha_emision)),
                        'razon' => 'ANULA DOCUMENTO',
                        'codigo' => 1,
                    ],
                ];
            }
        }

        return view('pages.ventas.notascredito.create', [
            'clientes' => $clientes, 'unidades' => $unidades, 'comunas' => $comunas,
            'emisor' => $emisor, 'listas' => $listas, 'proyectos' => $proyectos, 'prefill' => $prefill,
        ]);
    }

    public function showSelector(Request $request){
        $documentos = Factura::where('estado', 'NOT LIKE', '3%')->with('cliente')->get();
        return view('pages.ventas.notascredito.selector', ['documentos' => $documentos]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = NotaCredito::where('estado', '!=', -1)->with('cliente');
        return DataTables::eloquent($data)->toJson();
    }

    public function storeAnulacion(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'tipo_doc' => 'required',
                'folio' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            // Se declara una variable vacia para almacenar el documento
            $doc = null;
            // Si el documento es una factura, buscamos en la tabla facturas
            if($request->tipo_doc == 33){
                // Encontramos el documento y lo almacenamos
                $doc = Factura::where('folio', $request->folio)->with('lineas')->first();
            }

            if($doc == null){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'El documento no existe en la base de datos',
                ]);
            }

            $emisor = Ajustes::getEmisor();
            $cliente = Cliente::where('id', $doc->cliente_id)->with('comuna')->first();


            $str = date('Y-m-d').' '.date('H:i');
            $detalle = [];
            foreach($doc->lineas as $item){
                array_push($detalle, [
                    'nombre' => $item->nombre,
                    'descripcion' => $item->descripcion,
                    'unidad' => $item->unidad,
                    'precio' => $item->precio_unitario,
                    'cantidad' => $item->cantidad
                ]);
            }
            $detalle = array_filter($detalle);
            $detalleApi = array_map(function($item){
                unset($item['descripcion']);
                return $item;
            }, $detalle);
            $data = [
                'acteco' => $emisor['acteco'],
                'tipo' => 61,
                'fecha' => $str,
                'receptor' => [
                    'rut'=> $cliente->rut,
                    'razon_social'=> $cliente->razon_social,
                    'giro'=> $cliente->giro,
                    'direccion'=> $cliente->direccion,
                    'comuna'=> $cliente->comuna->nombre,
                ],
                'forma_pago' => $doc->tipo_pago,
                'detalles' => $detalleApi,
                'referencias' => [
                    [
                    'tipo' => 33,
                    'fecha' => date('Y-m-d', strtotime($doc->fecha_emision)),
                    'folio' => $doc->folio,
                    'razon' => 'ANULA DOCUMENTO',
                    'codigo' => 1
                    ]
                ],
                'correo_dte' => $cliente->email_dte
            ];

            $docData = $this->facturapi->emitirDocumento($data);
            if(!($docData->success ?? false)){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'No se pudo generar el documento en la API',
                    'error' => $docData->error ?? ($docData->msg ?? null)
                ]);
            }

            $nc = new NotaCredito();
            $nc->folio = $docData->folio;
            Log::info($str);
            $nc->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $nc->cliente_id = $doc->cliente_id;
            $nc->user_id = auth()->user()->id;
            $nc->tipo_pago = $doc->tipo_pago;
            $nc->descuento = 0;
            $nc->monto_neto = $docData->totales->neto;
            $nc->monto_iva = $docData->totales->iva;
            $nc->monto_total = $docData->totales->total;
            $nc->estado = '00';
            $nc->track_id = $docData->trackid ?? null;
            if(isset($request->glosa)){
                $nc->glosa = str_replace('///', '<br>', $request->glosa);
            }
            $nc->proyecto_id = $doc->proyecto_id;
            $nc->save();

            $factEstado = $doc->estado;
            $factEstado = substr($factEstado, 1); // se quita el primer caracter del string estado '000' => '00'
            $newEstado = '3'.$factEstado; // se concatena el nuevo estado con el string estado anterior '3' + '00' => '300'
            $doc->estado = $newEstado; // se asigna el valor del nuevo estado
            $doc->save(); // se almacena el documento con el nuevo estado

            // Se recorre listado de productos NC y se almacenan
            $subtotal = 0;
            foreach($doc->lineas as $item){
                $linea = new LineaNC();
                $linea->sku = $item->sku;
                $linea->nombre = $item->nombre;
                $linea->descripcion = $item->descripcion;
                $linea->cantidad = $item->cantidad;
                $linea->unidad = $item->unidad;
                $linea->precio_unitario = $item->precio_unitario;
                $linea->descuento = $item->descuento;
                $linea->nota_credito_id = $nc->id;
                $linea->save();
            }

            $pendiente = new DocumentoPendiente();
            $pendiente->tipo_doc = 61;
            $pendiente->folio = $nc->folio;
            $pendiente->track_id = $docData->trackid;
            $pendiente->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'result' => $docData
            ]);
        }catch(Exception $ex){
            Log::error('Usuario conectado: '.auth()->user());
            Log::error($ex);
            return response()->json([
                'success' => 'false',
                'msg' => 'No se pudo generar el documento',
                'error' => $ex->getMessage()
            ]);
        }
    }

    /** Valida el request de emisión; devuelve null si es válido o la respuesta JSON de error. */
    private function validarEmision(Request $request, bool $requiereProyecto){
        $rules = [
            'fecha_emision' => 'required',
            'cliente' => 'required',
            'items' => 'required|array|min:1',
            'referencias' => 'required|array|min:1',
            'glosa' => 'nullable|max:250',
        ];
        if($requiereProyecto) $rules['proyecto'] = 'required';
        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()){
            return response()->json([
                'success' => 'false',
                'msg' => 'La información ingresada no es suficiente para completar el registro',
                'error' => $validator->errors()
            ]);
        }
        $errRef = NotaCreditoRef::validarReferencias($request->referencias);
        if(!empty($errRef)){
            return response()->json(['success' => 'false', 'msg' => $errRef[0], 'error' => implode(' | ', $errRef)]);
        }
        if(NotaCreditoRef::correccionTextoConMontos($request->referencias, $request->items)
            && !$request->boolean('confirmar_correccion_texto_sin_montos')){
            return response()->json([
                'success' => 'false',
                'msg' => 'Una nota de crédito que corrige texto (código 2) se emite con total $0. Elimine los montos de las líneas o cambie el código de referencia a 3.'
            ]);
        }
        return null;
    }

    public function vistaPreviaEnvioNC(Request $request){
        set_time_limit(300);
        try{
            if($error = $this->validarEmision($request, true)) return $error;

            $emisor = Ajustes::getEmisor();
            $cliente = Cliente::where('id', $request->cliente)->with('comuna')->first();
            $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');

            $detalle = [];
            foreach($request->items as $item){
                $detalle[] = [
                    'NmbItem' => mb_convert_encoding($item['nombre'], 'UTF-8', 'ISO-8859-1'),
                    'DscItem' => ((!array_key_exists('descripcion', $item))?false:Herramientas::sanitizarString($item['descripcion'])),
                    'UnmdItem' => Unidad::find($item['unidad'])->abreviacion,
                    'PrcItem' => $item['precio'],
                    'QtyItem' => $item['cantidad'],
                    'MontoItem' => $item['precio'] * $item['cantidad'],
                ];
            }
            $referencias = [];
            $linea = 1;
            foreach(NotaCreditoRef::referencias($request->referencias) as $ref){
                $referencias[] = [
                    'NroLinRef' => $linea++,
                    'TpoDocRef' => $ref['tipo'],
                    'FolioRef' => $ref['folio'],
                    'FchRef' => $ref['fecha'],
                    'RazonRef' => $ref['razon'],
                    'CodRef' => $ref['codigo'],
                ];
            }
            $t = NotaCreditoRef::totales($request->items);

            $dte = [
                'Encabezado' => [
                    'IdDoc' => ['TipoDTE' => 61, 'Folio' => 'SIN FOLIO', 'FchEmis' => $str, 'FmaPago' => $request->tipo_pago ?? 1],
                    'Emisor' => [
                        'RUTEmisor' => $emisor['rut'],
                        'RznSoc' => $emisor['razon_social'],
                        'GiroEmis' => $emisor['giro'],
                        'Acteco' => $emisor['acteco'],
                        'DirOrigen' => $emisor['direccion'],
                        'CmnaOrigen' => mb_convert_encoding(Comuna::find($emisor['comuna'])->nombre, 'UTF-8', 'ISO-8859-1'),
                    ],
                    'Receptor' => [
                        'RUTRecep' => $cliente->rut,
                        'RznSocRecep' => mb_convert_encoding($cliente->razon_social, 'UTF-8', 'ISO-8859-1'),
                        'GiroRecep' => mb_convert_encoding($cliente->giro, 'UTF-8', 'ISO-8859-1'),
                        'DirRecep' => mb_convert_encoding($cliente->direccion, 'UTF-8', 'ISO-8859-1'),
                        'CmnaRecep' => mb_convert_encoding($cliente->comuna->nombre, 'UTF-8', 'ISO-8859-1'),
                        'CdgIntRecep' => 'CASA MATRIZ'
                    ],
                    'Totales' => ['MntNeto' => $t['neto'], 'IVA' => $t['iva'], 'MntTotal' => $t['total']]
                ],
                'Detalle' => array_filter($detalle),
                'Referencia' => $referencias
            ];
            $pdf = new \SolucionTotal\CorePDF\PDF($dte, 1, 'https://intranet.joremet.cl/logo_joremet.png', 2);
            $pdf->setCedible(false);
            $pdf->setTelefono($emisor['telefono']);
            $pdf->setWeb($emisor['web']);
            $pdf->setMail($emisor['email']);
            $pdf->setMarcaAgua('https://intranet.joremet.cl/logo_joremet.png');
            $pdf->setGlosa(str_replace('//', '<br>', $request->glosa));
            $pdf->setObra(Proyecto::find($request->proyecto)->nombre);
            $pdf->construir();
            return response()->json(['PDF' => base64_encode($pdf->generar(3)."?".time())]);
        }catch(Exception $ex){
            Log::error($ex);
            return response()->json(['success' => 'false', 'msg' => 'No se pudo generar la vista previa', 'error' => $ex->getMessage()]);
        }
    }

    public function storeNotaCredito(Request $request){
        try{
            if($error = $this->validarEmision($request, true)) return $error;

            $emisor = Ajustes::getEmisor();
            $cliente = Cliente::where('id', $request->cliente)->with('comuna')->first();
            $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');

            $detalleApi = [];
            foreach($request->items as $item){
                $detalleApi[] = [
                    'nombre' => $item['nombre'],
                    'unidad' => Unidad::find($item['unidad'])->abreviacion,
                    'precio' => $item['precio'],
                    'cantidad' => $item['cantidad']
                ];
            }

            $data = [
                'acteco' => $emisor['acteco'],
                'tipo' => 61,
                'fecha' => $str,
                'receptor' => [
                    'rut'=> $cliente->rut,
                    'razon_social'=> $cliente->razon_social,
                    'giro'=> $cliente->giro,
                    'direccion'=> $cliente->direccion,
                    'comuna'=> Herramientas::sanitizarString($cliente->comuna->nombre),
                ],
                'forma_pago' => $request->tipo_pago ?? 1,
                'detalles' => $detalleApi,
                'referencias' => NotaCreditoRef::referencias($request->referencias),
                'correo_dte' => $cliente->email_dte
            ];

            Log::info("Datos enviados NC:");
            Log::info($data);
            $docData = $this->facturapi->emitirDocumento($data);
            Log::info("Datos recibidos NC:");
            Log::info(json_encode($docData));
            if(!($docData->success ?? false)){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'No se pudo generar el documento en la API',
                    'error' => $docData->msg ?? ($docData->error ?? null),
                    'errors' => $docData->errors ?? null
                ]);
            }

            $nc = new NotaCredito();
            $nc->folio = $docData->folio;
            $nc->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $nc->cliente_id = $request->cliente;
            $nc->user_id = auth()->user()->id;
            $nc->tipo_pago = $request->tipo_pago ?? 1;
            $nc->tipo_descuento = 0;
            $nc->descuento = 0;
            $nc->estado = '00';
            $nc->track_id = $docData->trackid ?? null;
            $nc->monto_neto = $docData->totales->neto;
            $nc->monto_iva = $docData->totales->iva;
            $nc->monto_total = $docData->totales->total;
            $nc->proyecto_id = $request->proyecto;
            if(isset($request->glosa)){
                $nc->glosa = str_replace('///', '<br>', $request->glosa);
            }
            $nc->save();

            foreach($request->items as $item){
                $linea = new LineaNC();
                $linea->sku = $item['sku'] ?? null;
                $linea->nombre = $item['nombre'];
                $linea->descripcion = $item['descripcion'] ?? '';
                $linea->cantidad = $item['cantidad'];
                $linea->unidad = Unidad::find($item['unidad'])->abreviacion;
                $linea->precio_unitario = $item['precio'];
                $linea->descuento = $item['descuento'] ?? 0;
                $linea->nota_credito_id = $nc->id;
                $linea->save();
            }

            $pendiente = new DocumentoPendiente();
            $pendiente->tipo_doc = 61;
            $pendiente->folio = $nc->folio;
            $pendiente->track_id = $docData->trackid ?? null;
            $pendiente->save();

            // Código 1 sobre factura 33 propia: se marca la factura como anulada (como storeAnulacion)
            foreach($request->referencias as $ref){
                if((int)($ref['codigo'] ?? 0) === 1 && (int)$ref['tipo'] === 33){
                    $fact = Factura::where('folio', $ref['folio'])->where('cliente_id', $request->cliente)->first();
                    if($fact && substr($fact->estado, 0, 1) !== '3'){
                        $fact->estado = '3'.substr($fact->estado, 1);
                        $fact->save();
                    }
                }
            }

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'result' => $docData
            ]);
        }catch(Exception $ex){
            Log::error('Usuario conectado: '.auth()->user());
            Log::error($ex);
            return response()->json([
                'success' => 'false',
                'msg' => 'No se pudo generar el documento',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function vistaPreviaNotaCredito(Request $request, $folio){
        try{
            $emisor = Ajustes::getEmisor();
            $nc = NotaCredito::with('cliente', 'cliente.comuna')->where('folio', $folio)->first();
            $result = $this->facturapi->obtenerXmlDocumentoPropio(61, $folio);

            $EnvioDTE = new EnvioDte();
            $EnvioDTE->loadXML($result);
            $dte = $EnvioDTE->getDocumentos()[0];
            $caratula = $EnvioDTE->getCaratula();
            $data = $dte->getDatos();

            $pdf = new \SolucionTotal\CorePDF\PDF($data, 1, 'https://i.imgur.com/oWL7WBw.jpeg', 2, $dte->getTED());
            $pdf->setCedible(false);
            //$pdf->setLeyendaImpresion('Sistema de facturacion por SoluciónTotal');
            $pdf->setObra($nc->proyecto->nombre);
            $pdf->setTelefono($emisor['telefono']);
            $pdf->setResolucion(date('Y', strtotime($caratula['FchResol'])), $caratula['NroResol']);
            $pdf->setWeb($emisor['web']);
            $pdf->setMail($emisor['email']);
            $pdf->setMarcaAgua('https://i.imgur.com/oWL7WBw.jpeg');
            $glosa = str_replace('//', '<br>', $nc->glosa);
            $pdf->setGlosa($glosa);
            $pdf->construir();
            $pdf->generar(1);
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
            $result = $this->facturapi->obtenerXmlDocumentoPropio(61, $folio);

            $headers = [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="DTET61F'. $folio .'.xml"',
                'Content-Transfer-Encoding' => 'binary'
            ];

            return response()->make($result, 200, $headers);
        }catch(Exception $ex){
            return $ex;
        }
    }
}
