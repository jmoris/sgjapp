<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\ListaPrecio;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaestroController extends Controller
{
    public function getUnidades(){
        $unidades = Unidad::all();
        return response()->json($unidades);
    }

    public function getCategorias(){
        $categorias = Categoria::all();
        return response()->json($categorias);
    }

    public function getListasPrecios(){
        $lista_precio = ListaPrecio::all();
        return response()->json($lista_precio);
    }

    public function storeUnidad(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|unique:tenant.unidads',
                'abreviacion' => 'required',

            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $unidad = new Unidad();
            $unidad->nombre = $request->nombre;
            $unidad->abreviacion = $request->abreviacion;
            $unidad->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $unidad
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function deleteUnidad($id){
        try{
            $unidad = Unidad::find($id);
            $unidad->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Información eliminada exitosamente',
                'data' => $unidad
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function storeCategoria(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'codigo_interno' => '',
                'nombre' => 'required|unique:tenant.categorias',
                'descripcion' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $categoria = new Categoria();
            $categoria->nombre = $request->nombre;
            $categoria->descripcion = $request->descripcion;
            $categoria->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $categoria
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function storeListaPrecio(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'codigo_interno' => '',
                'nombre' => 'required|unique:tenant.lista_precios',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $unidad = new Unidad();
            $unidad->nombre = $request->nombre;
            $unidad->abreviacion = $request->abreviacion;
            $unidad->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $unidad
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }
}
