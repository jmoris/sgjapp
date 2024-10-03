<?php

namespace App\Http\Controllers;

use App\Borrador;
use App\InfoBorrador;
use App\LineaBorrador;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BorradorController extends Controller
{
    /**
     * Metodo que retorna todos los borradores existentes
     */
    public function getAll(Request $request){
        $borradores = Borrador::where('user_id', auth()->user()->id)->get();
        return $borradores;
    }
    /**
     * Metodo que retorna un borrador en especifico
     */
    public function getBorrador(Request $request, $id){
        try{
            $borrador = Borrador::with('lineas', 'datos')->findOrFail($id);
            return $borrador;
        }catch(Exception $ex){
            return response()->json([
                'success' => 'false',
                'msg' => 'Hubo un error al intentar obtener el borrador',
                'error' => $ex->getMessage()
            ]);
        }
    }
    /**
     * Metodo para guardar un borrador nuevo
     */
    public function storeBorrador(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'tipo_doc' => 'required|integer',
                'fecha_emision' => 'required',
                'externo' => 'required',
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
            $borrador = null;
            if($request->id != null){
                $borrador = Borrador::findOrFail($request->id);
            }else{
                $borrador = new Borrador();
            }
            $str = date('Y-m-d', strtotime($request->fecha_emision)).' '.date('H:i');
            $borrador->tipo_doc = $request->tipo_doc;
            $borrador->externo_id = $request->externo;
            $borrador->fecha_emision = date('Y-m-d H:i', strtotime($str));
            $borrador->user_id = auth()->user()->id;
            $borrador->proyecto_id = $request->proyecto;
            if(isset($request->glosa)||$request->glosa!=null){
                $borrador->glosa = str_replace('///', '<br>', $request->glosa);
            }
            $borrador->save();
            // Se recorre listado de productos OC y se almacenan
            $subtotal = 0;
            LineaBorrador::where('borrador_id', $borrador->id)->delete();
            foreach($request->items as $item){
                $linea = new LineaBorrador();
                $linea->sku = $item['sku'];
                $linea->nombre = $item['nombre'];
                $linea->descripcion = ((!array_key_exists('descripcion', $item))?'':$item['descripcion']);
                $linea->unidad = Unidad ::find($item['unidad'])->abreviacion;
                $linea->cantidad = $item['cantidad'];
                $linea->stock = ((!array_key_exists('stock', $item))?0:$item['stock']);
                $linea->recibido = ((!array_key_exists('recibido', $item))?0:$item['recibido']);
                $linea->precio_unitario = ((!array_key_exists('precio_unitario', $item))?0:$item['precio_unitario']);
                $linea->largo = ((!array_key_exists('largo', $item))?0:$item['largo']);
                $linea->ancho = ((!array_key_exists('ancho', $item))?0:$item['ancho']);
                $linea->peso = ((!array_key_exists('peso', $item))?0:$item['peso']);
                $linea->borrador_id = $borrador['id'];
                $linea->descuento = ((!array_key_exists('descuento', $item))?0:$item['descuento']);
                $linea->save();
                $subtotal += intval($linea->precio_unitario * $linea->cantidad);
            }
            Log::info($request->info);
            InfoBorrador::where('borrador_id', $borrador->id)->delete();
            foreach($request->info as $info){
                $linea = new InfoBorrador();
                $linea->nombre = $info['nombre'];
                $linea->valor = $info['valor'];
                $linea->borrador_id = $borrador['id'];
                $linea->save();
            }

            return response()->json([
                'success' => true,
                'tipo' => ($request->id!=null)?'actualizar':'guardar',
                'msg' => 'Información guardada exitosamente',
                'data' => $borrador
            ]);
        }catch(Exception $ex){
            Log::error('Usuario conectado: '.auth()->user());
            Log::error($ex);
            return $ex;
        }
    }
}
