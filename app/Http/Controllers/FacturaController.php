<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Comuna;
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
        $data = Factura::where('estado', '!=', -1)->with('cliente');
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
            $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');

            $emisor = Ajustes::getEmisor();
            $cliente = Cliente::where('id', $request->cliente)->with('comuna')->first();
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
                'detalles' => $request->items,
                'referencias' => []
            ];

            $ch = curl_init( 'https://dev.facturapi.cl/api/documentos' );
            curl_setopt( $ch, CURLOPT_POST, true);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data) );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: Bearer 2|cFBrnEmGhb6eFM9pCUNv55WAWUSV7TcAegK2pAHZda9482c3'
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
            $fact->descuento = 1;
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
            $dte = [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 33,
                        'Folio' => $fact->folio,
                        'FchEmis' => date('Y-m-d', strtotime($fact->fecha_emision)),
                        'FchVenc' => date('Y-m-d'),
                        'FmaPago' => $fact->tipo_pago,
                    ],
                    'Emisor' => [
                        'RUTEmisor' => $emisor['rut'],
                        'RznSoc' => $emisor['razon_social'],
                        'GiroEmis' => $emisor['giro'],
                        'Acteco' => 251100,
                        'DirOrigen' => $emisor['direccion'],
                        'CmnaOrigen' => 'TENO',
                    ],
                    'Receptor' => [
                        'RUTRecep' => $fact->cliente->rut,
                        'RznSocRecep' => $fact->cliente->razon_social,
                        'GiroRecep' => $fact->cliente->giro,
                        'DirRecep' => $fact->cliente->direccion,
                        'CmnaRecep' => $fact->cliente->comuna->nombre,
                        'CdgIntRecep' => 'CASA MATRIZ'
                    ],
                    'Totales' => [
                        'MntNeto' => $fact->monto_neto,
                        'IVA' => $fact->monto_iva,
                        'MntTotal' => $fact->monto_total,
                    ]
                ],
                'Detalle' => [],
                'Referencia' => []
            ];
            $subtotal = 0;
            $lineas = LineaFactura::where('factura_id', $fact->id)->get();
            foreach($lineas as $linea){
                array_push($dte['Detalle'], [
                    'NmbItem' => $linea->nombre,
                    'DscItem' => $linea->descripcion,
                    'QtyItem' => $linea->cantidad,
                    'PrcItem' => $linea->precio_unitario,
                    'MontoItem' => $linea->precio_unitario * $linea->cantidad,
                    'UnmdItem' => $linea->unidad
                ]);
                $subtotal += $linea->precio_unitario * $linea->cantidad;
            }

            $pdf = new \SolucionTotal\CorePDF\PDF($dte, 1, 'https://i.imgur.com/oWL7WBw.jpeg', 2);
            $pdf->setCedible(false);
            //$pdf->setLeyendaImpresion('Sistema de facturacion por SoluciónTotal');
            $pdf->setTelefono("75 2 412060");
            $pdf->setWeb('www.joremet.cl');
            $pdf->setMail("contacto@joremet.cl");
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
