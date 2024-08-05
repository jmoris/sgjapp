<?php

namespace App\Http\Controllers;

use App\Comuna;
use App\Config;
use App\Helpers\Ajustes;
use App\LineaOC;
use App\OrdenCompra;
use App\Producto;
use App\Proveedor;
use App\Proyecto;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OrdenCompraController extends Controller
{
    public function index(){
        return view('pages.compras.ordenescompra.index');
    }

    public function newOC(){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $proveedores = Proveedor::all();
        $unidades = Unidad::all();
        $proyectos = Proyecto::where('estado', 0)->get();
        return view('pages.compras.ordenescompra.create', ['proveedores' => $proveedores, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'proyectos' => $proyectos]);
    }

    public function editOC($id){
        $oc = OrdenCompra::with('proveedor')->find($id);
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $proveedores = Proveedor::all();
        $unidades = Unidad::all();
        $proyectos = Proyecto::where('estado', 0)->get();
        return view('pages.compras.ordenescompra.edit', ['oc' => $oc, 'proveedores' => $proveedores, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'proyectos' => $proyectos]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = OrdenCompra::where('rev_activa', true)->where('estado', '!=', -1)->with('proveedor');
        return DataTables::eloquent($data)->toJson();
    }

    public function getById(Request $request, $id){
        try{
            $user = OrdenCompra::findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'fecha_emision' => 'required',
                'proveedor' => 'required',
                'tipo_pago' => 'required',
                'items' => 'required|array',
                'proyecto' => 'required',
                'glosa' => 'nullable'
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }
            $ocs = OrdenCompra::orderBy('folio', 'desc')->first();
            if($ocs == null){
                $folio = 500;
            }else{
                $folio = $ocs->folio;
            }
            $oc = new OrdenCompra();
            $oc->folio = $folio+1;
            $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');
            Log::info($str);
            $oc->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $oc->proveedor_id = $request->proveedor;
            $oc->user_id = auth()->user()->id;
            $oc->proyecto_id = $request->proyecto;
            $oc->rev_activa = true;
            $oc->tipo_pago = $request->tipo_pago;
            $oc->descuento = 1;
            $oc->monto_neto = 0;
            $oc->monto_iva = 0;
            $oc->monto_total = 0;
            if(isset($request->glosa)){
                $oc->glosa = str_replace('///', '<br>', $request->glosa);
            }
            $oc->tipo_pago = $request->tipo_pago;
            $oc->save();
            // Se recorre listado de productos OC y se almacenan
            $subtotal = 0;
            foreach($request->items as $item){
                $linea = new LineaOC();
                $linea->sku = $item['sku'];
                $linea->nombre = $item['nombre'];
                $linea->descripcion = ((!array_key_exists('descripcion', $item))?'':$item['descripcion']);
                $linea->cantidad = $item['cantidad'];
                $linea->unidad = Unidad::find($item['unidad'])->abreviacion;
                $linea->precio_unitario = $item['precio'];
                $linea->descuento = ((!array_key_exists('descuento', $item))?0:$item['descuento']);
                $linea->orden_compra_id = $oc['id'];
                $linea->save();
                $subtotal += intval($linea->precio_unitario * $linea->cantidad);
            }
            $neto = $subtotal;
            $iva = intval($neto * 0.19);
            $total = $neto + $iva;

            OrdenCompra::where('id', $oc->id)->update([
                'monto_neto' => $neto,
                'monto_iva' => $iva,
                'monto_total' => $total
            ]);

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
            ]);
        }catch(Exception $ex){
            Log::error('Usuario conectado: '.auth()->user());
            Log::error($ex);
            return $ex;
        }
    }

    public function update(Request $request, $id){
        try{
                $validator = Validator::make($request->all(), [
                    'fecha_emision' => 'required',
                    'proveedor' => 'required',
                    'tipo_pago' => 'required',
                    'items' => 'required|array',
                    'proyecto' => 'required',
                    'glosa' => 'nullable'
                ]);

                if($validator->fails()){
                    return response()->json([
                        'success' => 'false',
                        'msg' => 'La información ingresada no es suficiente para completar el registro',
                        'error' => $validator->errors()
                    ]);
                }
                $first_oc = OrdenCompra::findOrFail($id);

                OrdenCompra::where('folio', $first_oc->folio)->update(['rev_activa' => false]);

                $oc = new OrdenCompra();
                $oc->folio = $first_oc->folio;
                $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');
                $oc->fecha_emision = date('Y-m-d H:i', strtotime($str));
                $oc->proveedor_id = $request->proveedor;
                $oc->user_id = auth()->user()->id;
                $oc->proyecto_id = $request->proyecto;
                $oc->tipo_pago = $request->tipo_pago;
                $oc->rev = (OrdenCompra::orderBy('rev', 'desc')->where('folio', $first_oc->folio)->first())->rev + 1;
                $oc->rev_activa = true;
                $oc->descuento = 1;
                $oc->monto_neto = 0;
                $oc->monto_iva = 0;
                $oc->monto_total = 0;
                if(isset($request->glosa)){
                    $oc->glosa = str_replace('///', '<br>', $request->glosa);
                }
                $oc->tipo_pago = $request->tipo_pago;
                $oc->save();
                // Se recorre listado de productos OC y se almacenan
                $subtotal = 0;
                foreach($request->items as $item){
                    $linea = new LineaOC();
                    $linea->sku = $item['sku'];
                    $linea->nombre = $item['nombre'];
                    $linea->descripcion = ((!array_key_exists('descripcion', $item))?'':$item['descripcion']);
                    $linea->cantidad = $item['cantidad'];
                    $linea->unidad = Unidad::find($item['unidad'])->abreviacion;
                    $linea->precio_unitario = $item['precio'];
                    $linea->descuento = ((!array_key_exists('descuento', $item))?0:$item['descuento']);
                    $linea->orden_compra_id = $oc['id'];
                    $linea->save();
                    $subtotal += intval($linea->precio_unitario * $linea->cantidad);
                }
                $neto = $subtotal;
                $iva = intval(round($neto * 0.19));
                $total = $neto + $iva;

                OrdenCompra::where('id', $oc->id)->update([
                    'monto_neto' => $neto,
                    'monto_iva' => $iva,
                    'monto_total' => $total
                ]);

                return response()->json([
                    'success' => true,
                    'msg' => 'Información guardada exitosamente',
                ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function delete(Request $request, $id){
        try{
            $oc = OrdenCompra::find($id);
            $oc->estado = -1;
            $oc->save();
            return response()->json([
                'success' => true,
                'msg' => 'Información anulada exitosamente',
                'data' => $oc
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function vistaPreviaOC(Request $request, $folio, $rev = 1){
        try{
            $emisor = Ajustes::getEmisor();
            $oc = OrdenCompra::with('proveedor', 'proveedor.comuna')->where('folio', $folio)->where('rev', $rev)->first();
            $dte = [
                'Encabezado' => [
                    'IdDoc' => [
                        'TipoDTE' => 801,
                        'Folio' => $oc->folio,
                        'FchEmis' => date('Y-m-d', strtotime($oc->fecha_emision)),
                        'FchVenc' => date('Y-m-d'),
                        'FmaPago' => $oc->tipo_pago,
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
                        'RUTRecep' => $oc->proveedor->rut,
                        'RznSocRecep' => $oc->proveedor->razon_social,
                        'GiroRecep' => $oc->proveedor->giro,
                        'DirRecep' => $oc->proveedor->direccion,
                        'CmnaRecep' => $oc->proveedor->comuna->nombre,
                        'CdgIntRecep' => 'CASA MATRIZ'
                    ],
                    'Totales' => [
                        'MntNeto' => $oc->monto_neto,
                        'IVA' => $oc->monto_iva,
                        'MntTotal' => $oc->monto_total,
                    ]
                ],
                'Detalle' => []
            ];
            $subtotal = 0;
            $lineas = LineaOC::where('orden_compra_id', $oc->id)->get();
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
            $glosa = str_replace('//', '<br>', $oc->glosa);
            $pdf->setGlosa($glosa);
            $pdf->setObra($oc->proyecto->nombre);
            $pdf->setLeyendaImpresion("Rev. ". $rev);
            $pdf->setFirmaDerecha("Validado por", "");
            $pdf->setFirmaIzquierda($oc->usuario->cargo, $oc->usuario->name.' '.$oc->usuario->lastname);
            //$pdf->setFirmaIzquierda($oc->usuario->cargo, "<img style='height: 80px;' src='https://i.postimg.cc/j29cg3BZ/Jesus-Moris.png'>");
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
