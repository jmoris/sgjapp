<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Comuna;
use App\DocumentoPendiente;
use App\Factura;
use App\Helpers\Ajustes;
use App\LineaNC;
use App\ListaPrecio;
use App\NotaCredito;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use SolucionTotal\CoreDTE\Sii\EnvioDte;
use Yajra\DataTables\Facades\DataTables;

class NotaCreditoController extends Controller
{
    public function index(){
        return view('pages.ventas.notascredito.index');
    }

    public function newNotaCredito(){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $clientes = Cliente::all(); // aqui clientes
        $unidades = Unidad::all();
        $listas = ListaPrecio::all();
        return view('pages.ventas.notascredito.create', ['clientes' => $clientes, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'listas' => $listas]);
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
            $data = [
                'contribuyente' => $emisor['rut'],
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
                'tipo_pago' => $doc->tipo_pago,
                'detalles' => $detalle,
                'referencias' => [
                    [
                    'tipo' => 33,
                    'fecha' => date('Y-m-d', strtotime($doc->fecha_emision)),
                    'folio' => $doc->folio,
                    'razon' => 'ANULA DOCUMENTO',
                    'codigo' => 1
                    ]
                ]
            ];

            $ch = curl_init( env('FACTURAPI_ENDPOINT').'documentos' );
            curl_setopt( $ch, CURLOPT_POST, true);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer '.env('FACTURAPI_TOKEN')
            ]);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            $result = curl_exec($ch);
            curl_close($ch);

            $docData = json_decode($result);

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
                'result' => $result
            ]);
        }catch(Exception $ex){
            Log::error('Usuario conectado: '.auth()->user());
            Log::error($ex);
            return $ex;
        }
    }

    public function vistaPreviaNotaCredito(Request $request, $folio){
        try{
            $emisor = Ajustes::getEmisor();
            $nc = NotaCredito::with('cliente', 'cliente.comuna')->where('folio', $folio)->first();
            $ch = curl_init( env('FACTURAPI_ENDPOINT').'documentos/generar/xml/61/'.$folio.'?contribuyente='.$emisor['rut']);
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
}
