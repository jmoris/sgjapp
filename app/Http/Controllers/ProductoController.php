<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\ListaPrecio;
use App\Producto;
use App\Proveedor;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProductoController extends Controller
{
    public function index(){
        return view('pages.productos.index');
    }

    public function newProducto(){
        $unidades = Unidad::all();
        $categorias = Categoria::all();
        $listaprecios = ListaPrecio::all();
        return view('pages.productos.create', ['unidades'=>$unidades, 'categorias'=>$categorias, 'lista_precios'=>$listaprecios]);
    }

    public function editProducto($id){
        $producto = Producto::with('precios','proveedores')->find($id);
        $unidades = Unidad::all();
        $categorias = Categoria::all();
        $listaprecios = ListaPrecio::all();
        $proveedores = Proveedor::all();
        return view('pages.productos.edit', ['producto'=>$producto, 'unidades'=>$unidades, 'categorias'=>$categorias, 'lista_precios'=>$listaprecios, 'proveedores' => $proveedores]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = Producto::with('unidades');
        return DataTables::eloquent($data)->toJson();
    }

    public function getProductosCompra(){
        try{
            $productos = Producto::where('se_compra', 1)->get();
            return response()->json($productos);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function getById(Request $request, $id){
        try{
            $user = Producto::findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function addPrecioLista(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'lista_precio_id' => 'required',
                'producto_id' => 'required',
                'precio' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $producto = Producto::findOrFail($request->producto_id);
            $producto->lista_precios()->sync([$request->lista_precio_id, ['precio' => $request->precio, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]], false);


            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $producto
            ]);
        }catch(Exception $ex){
            Log::info($ex);
            return response()->json([
                'success' => 'false',
                'msg' => 'Hubo un error al enlazar el precio del producto con la lista de precios',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function addPrecioProveedor(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'proveedor_id' => 'required',
                'producto_id' => 'required',
                'precio' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }
            $producto = Producto::findOrFail($request->producto_id);
            $count = $producto->proveedores()->where('proveedor_id', $request->proveedor_id)->get();
            if(count($count) == 0){
                $producto->proveedores()->attach($request->proveedor_id, ['precio' => $request->precio, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => date('Y-m-d h:i:s')]);
            }else{
                DB::table('producto_proveedor')->where('proveedor_id', $request->proveedor_id)->update(['precio' => $request->precio]);
            }

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $producto
            ]);
        }catch(Exception $ex){
            Log::info($ex);
            return response()->json([
                'success' => 'false',
                'msg' => 'Hubo un error al enlazar el precio del producto con el proveedor',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'sku' => 'unique:tenant.productos',
                'nombre' => 'required',
                'descripcion' => 'nullable',
                'categoria' => 'required',
                'unidad' => 'required',
                'largo' => 'nullable',
                'ancho' => 'nullable',
                'peso' => 'nullable',
                'es_afecto' => 'boolean',
                'se_vende' => 'boolean',
                'se_compra' => 'boolean',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $producto = new Producto();
            if($request->sku==null){
                $producto->sku = 'ITEM-'.(Producto::count()+1);
            }else{
                $producto->sku = $request->sku;
            }
            $producto->nombre = $request->nombre;
            $producto->descripcion = $request->descripcion;
            $producto->categoria_id = $request->categoria;
            $producto->unidad_id = $request->unidad;

            if($request->largo != null){
                $producto->largo = $request->largo;
            }
            if($request->ancho != null){
                $producto->ancho = $request->ancho;
            }
            if($request->peso != null){
                $producto->peso = $request->peso;
            }

            $producto->es_afecto = $request->es_afecto;
            $producto->se_vende = ($request->se_vende==null) ? false : true;
            $producto->se_compra = ($request->se_compra==null) ? false : true;
            $producto->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $producto
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function update(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'sku' => 'required',
                'nombre' => 'required',
                'descripcion' => 'nullable',
                'categoria' => 'required',
                'unidad' => 'required',
                'largo' => 'nullable',
                'ancho' => 'nullable',
                'peso' => 'nullable',
                'es_afecto' => 'boolean',
                'se_vende' => 'boolean',
                'se_compra' => 'boolean',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $producto = Producto::findOrFail($id);
            $producto->sku = $request->sku;
            $producto->nombre = $request->nombre;
            $producto->descripcion = $request->descripcion;
            $producto->categoria_id = $request->categoria;
            $producto->unidad_id = $request->unidad;

            if($request->largo != null){
                $producto->largo = $request->largo;
            }
            if($request->ancho != null){
                $producto->ancho = $request->ancho;
            }
            if($request->peso != null){
                $producto->peso = $request->peso;
            }

            $producto->es_afecto = $request->es_afecto;
            $producto->se_vende = ($request->se_vende==null) ? false : true;
            $producto->se_compra = ($request->se_compra==null) ? false : true;
            $producto->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información actualizada exitosamente',
                'data' => $producto
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function delete(Request $request, $id){
        try{
            $user = Producto::find($id);
            $user->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Información eliminada exitosamente',
                'data' => $user
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }
}
