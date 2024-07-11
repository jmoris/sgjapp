<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Comuna;
use App\GuiaDespacho;
use App\Helpers\Ajustes;
use App\LineaGuia;
use App\ListaPrecio;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use SolucionTotal\CoreDTE\Sii\Dte;
use SolucionTotal\CoreDTE\Sii\EnvioDte;
use Yajra\DataTables\Facades\DataTables;

class GuiaDespachoController extends Controller
{
    public function index(){
        return view('pages.ventas.guiasdespacho.index');
    }

    public function newGuiaDespacho(){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $clientes = Cliente::all(); // aqui clientes
        $unidades = Unidad::all();
        $listas = ListaPrecio::all();
        return view('pages.ventas.guiasdespacho.create', ['clientes' => $clientes, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'listas' => $listas]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = GuiaDespacho::with('cliente');
        return DataTables::eloquent($data)->toJson();
    }

    public function storeGuiaDespacho(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'fecha_emision' => 'required',
                'cliente' => 'required',
                'ind_traslado' => 'required',
                'tipo_despacho' => 'required',
                'comuna_destino' => 'required',
                'items' => 'required|array',
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
                    'descripcion' => ((!array_key_exists('descripcion', $item))?false:$item['descripcion']),
                    'unidad' => Unidad::find($item['unidad'])->abreviacion,
                    'precio' => $item['precio'],
                    'cantidad' => $item['cantidad']
                ]);
            }
            $detalle = array_filter($detalle);

            $data = [
                'contribuyente' => $emisor['rut'],
                'acteco' => $emisor['acteco'],
                'tipo' => 52,
                'fecha' => $str,
                'ind_traslado' => ($request->ind_traslado!=null)?$request->ind_traslado:null,
                'tipo_despacho' => ($request->tipo_despacho!=null)?$request->tipo_despacho:null,
                'comuna_destino' => $request->comuna_destino,
                'receptor' => [
                    'rut'=> $cliente->rut,
                    'razon_social'=> $cliente->razon_social,
                    'giro'=> $cliente->giro,
                    'direccion'=> $cliente->direccion,
                    'comuna'=> $cliente->comuna->nombre,
                ],
                'tipo_pago' => $request->tipo_pago,
                'detalles' => $detalle,
                'referencias' => []
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
            Log::info($result);
            curl_close($ch);
            $docData = json_decode($result);

            $guia = new GuiaDespacho();
            $guia->folio = $docData->folio;
            Log::info($str);
            $guia->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $guia->cliente_id = $request->cliente;
            $guia->user_id = auth()->user()->id;
            $guia->ind_traslado = $request->ind_traslado;
            $guia->tipo_despacho = $request->tipo_despacho;
            $guia->descuento = 1;
            $guia->monto_neto = $docData->totales->neto;
            $guia->monto_iva = $docData->totales->iva;
            $guia->monto_total = $docData->totales->total;
            if(isset($request->glosa)){
                $guia->glosa = str_replace('///', '<br>', $request->glosa);
            }
            $guia->save();
            // Se recorre listado de productos OC y se almacenan
            $subtotal = 0;
            foreach($request->items as $item){
                $linea = new LineaGuia();
                $linea->sku = $item['sku'];
                $linea->nombre = $item['nombre'];
                $linea->descripcion = ((!array_key_exists('descripcion', $item))?'':$item['descripcion']);
                $linea->cantidad = $item['cantidad'];
                $linea->unidad = Unidad::find($item['unidad'])->abreviacion;
                $linea->precio_unitario = $item['precio'];
                $linea->descuento = ((!array_key_exists('descuento', $item))?0:$item['descuento']);
                $linea->guia_despacho_id = $guia->id;
                $linea->save();
            }

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

    public function vistaPreviaGuiaDespacho(Request $request, $folio){
        try{
            $emisor = Ajustes::getEmisor();
            $guia = GuiaDespacho::with('cliente', 'cliente.comuna')->where('folio', $folio)->first();

            $ch = curl_init( env('FACTURAPI_ENDPOINT').'documentos/generar/xml/52/'.$folio.'?contribuyente='.$emisor['rut']);
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
            $pdf->setTelefono($emisor['telefono']);
            $pdf->setResolucion(date('Y', strtotime($caratula['FchResol'])), $caratula['NroResol']);
            $pdf->setWeb($emisor['web']);
            $pdf->setMail($emisor['email']);
            $pdf->setMarcaAgua('https://i.imgur.com/oWL7WBw.jpeg');
            $glosa = str_replace('//', '<br>', $guia->glosa);

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
