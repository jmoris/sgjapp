<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Comuna;
use App\DocumentoPendiente;
use App\Factura;
use App\Helpers\Ajustes;
use App\LineaFactura;
use App\ListaPrecio;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
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

    public function newFactura(){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $clientes = Cliente::all(); // aqui clientes
        $unidades = Unidad::all();
        $listas = ListaPrecio::all();

        return view('pages.ventas.facturas.create', ['clientes' => $clientes, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'listas' => $listas]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = Factura::with('cliente');
        return DataTables::eloquent($data)->toJson();
    }

    public function storeFactura(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'fecha_emision' => 'required',
                'cliente' => 'required',
                'tipo_pago' => 'required',
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
                'tipo' => 33,
                'fecha' => $str,
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
            curl_close($ch);

            $docData = json_decode($result);

            $fact = new Factura();
            $fact->folio = $docData->folio;
            Log::info($str);
            $fact->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $fact->cliente_id = $request->cliente;
            $fact->user_id = auth()->user()->id;
            $fact->tipo_pago = $request->tipo_pago;
            $fact->tipo_descuento = 0;
            $fact->descuento = 0;
            $fact->estado = '000';
            $fact->monto_neto = $docData->totales->neto;
            $fact->monto_iva = $docData->totales->iva;
            $fact->monto_total = $docData->totales->total;
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
            $emisor = Ajustes::getEmisor();
            $fact = Factura::with('cliente', 'cliente.comuna')->where('folio', $folio)->first();
            $ch = curl_init( env('FACTURAPI_ENDPOINT').'documentos/generar/xml/33/'.$folio.'?contribuyente='.$emisor['rut']);
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
            $glosa = str_replace('//', '<br>', $fact->glosa);
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
