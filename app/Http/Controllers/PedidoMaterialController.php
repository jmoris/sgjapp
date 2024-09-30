<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Comuna;
use App\Helpers\Ajustes;
use App\LineaPM;
use App\PedidoMaterial;
use App\Proveedor;
use App\Proyecto;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PedidoMaterialController extends Controller
{
    public function index(){
        return view('pages.compras.pedidosmateriales.index');
    }

    public function newPedido(){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $clientes = Cliente::all();
        $unidades = Unidad::all();
        $proyectos = Proyecto::where('estado', 0)->get();
        return view('pages.compras.pedidosmateriales.create', ['clientes' => $clientes, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'proyectos' => $proyectos]);
    }

    public function editPedido($id){
        $pedido = PedidoMaterial::with('mandante')->find($id);
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $clientes = Cliente::all();
        $unidades = Unidad::all();
        $proyectos = Proyecto::where('estado', 0)->get();
        return view('pages.compras.pedidosmateriales.edit', ['pedido' => $pedido, 'clientes' => $clientes, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'proyectos' => $proyectos]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = PedidoMaterial::where('rev_activa', true)->where('estado', '!=', -1)->with('mandante');
        return DataTables::eloquent($data)->toJson();
    }

    public function getById(Request $request, $id){
        try{
            $user = PedidoMaterial::findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function storePedido(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'fecha_emision' => 'required',
                'mandante' => 'required',
                'materia' => 'required',
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
            $ocs = PedidoMaterial::orderBy('folio', 'desc')->first();
            if($ocs == null){
                $folio = 1;
            }else{
                $folio = $ocs->folio;
            }
            $pm = new PedidoMaterial();
            $pm->folio = $folio+1;
            $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');
            Log::info($str);
            $pm->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $pm->cliente_id = $request->mandante;
            $pm->user_id = auth()->user()->id;
            $pm->proyecto_id = $request->proyecto;
            $pm->rev = 1;
            $pm->rev_activa = true;
            $pm->materia = $request->materia;
            $pm->peso_total = 0;
            $pm->peso_faltante = 0;
            $pm->peso_recibido = 0;
            if(isset($request->glosa)){
                $pm->glosa = str_replace('///', '<br>', $request->glosa);
            }
            $pm->save();
            // Se recorre listado de productos OC y se almacenan
            $total = 0;
            $faltante = 0;
            $recibido = 0;
            foreach($request->items as $item){
                $linea = new LineaPM();
                $linea->sku = $item['sku'];
                $linea->nombre = $item['nombre'];
                $linea->descripcion = ((!array_key_exists('descripcion', $item))?'':$item['descripcion']);
                $linea->cantidad = $item['cantidad'];
                $linea->stock = $item['stock'];
                $linea->recibido = $item['recibido'];
                $linea->unidad = Unidad::find($item['unidad'])->abreviacion;
                $linea->precio_unitario = (isset($item['precio']))?$item['precio']:0;
                $linea->largo = $item['largo'];
                $linea->ancho = (isset($item['ancho']))?$item['ancho']:1;
                $linea->peso = $item['peso'];
                $linea->pedido_material_id = $pm['id'];
                $linea->save();
                $total += intval($linea->largo * $linea->peso * $linea->cantidad);
                $recibido += intval($linea->largo * $linea->peso * ($linea->stock+$linea->recibido));
            }
            $faltante = $total - $recibido;

            PedidoMaterial::where('id', $pm->id)->update([
                'peso_total' => $total,
                'peso_faltante' => $faltante,
                'peso_recibido' => $recibido
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
                'mandante' => 'required',
                'materia' => 'required',
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
            $pms = PedidoMaterial::orderBy('folio', 'desc')->first();
            if($pms == null){
                $folio = 1;
            }else{
                $folio = $pms->folio;
            }
            $old_pm = PedidoMaterial::findOrFail($id);
            $old_pm->rev_activa = false;
            $old_pm->save();

            $pm = new PedidoMaterial();
            $pm->folio = $old_pm->folio;
            $str = date('Y-m-d', strtotime($old_pm->fecha_emision)).' '.date('H:i');
            Log::info($str);
            $pm->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $pm->cliente_id = $request->mandante;
            $pm->user_id = auth()->user()->id;
            $pm->proyecto_id = $request->proyecto;
            $pm->rev = $old_pm->rev + 1;
            $pm->rev_activa = true;
            $pm->materia = $request->materia;
            $pm->peso_total = 0;
            $pm->peso_faltante = 0;
            $pm->peso_recibido = 0;
            if(isset($request->glosa)){
                $pm->glosa = str_replace('///', '<br>', $request->glosa);
            }
            $pm->save();
            // Se recorre listado de productos OC y se almacenan
            $total = 0;
            $faltante = 0;
            $recibido = 0;
            foreach($request->items as $item){
                $linea = new LineaPM();
                $linea->sku = $item['sku'];
                $linea->nombre = $item['nombre'];
                $linea->descripcion = ((!array_key_exists('descripcion', $item))?'':$item['descripcion']);
                $linea->cantidad = $item['cantidad'];
                $linea->stock = $item['stock'];
                $linea->recibido = $item['recibido'];
                $linea->unidad = Unidad::find($item['unidad'])->abreviacion;
                $linea->precio_unitario = (isset($item['precio']))?$item['precio']:0;
                $linea->largo = $item['largo'];
                $linea->ancho = (isset($item['ancho']))?$item['ancho']:1;
                $linea->peso = $item['peso'];
                $linea->pedido_material_id = $pm['id'];
                $linea->save();
                $total += intval($linea->largo * $linea->peso * $linea->cantidad);
                $recibido += intval($linea->largo * $linea->peso * ($linea->stock+$linea->recibido));
            }
            $faltante = $total - $recibido;

            PedidoMaterial::where('id', $pm->id)->update([
                'peso_total' => $total,
                'peso_faltante' => $faltante,
                'peso_recibido' => $recibido
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
}
