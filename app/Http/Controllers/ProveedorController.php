<?php

namespace App\Http\Controllers;

use App\Comuna;
use App\Proveedor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProveedorController extends Controller
{
    public function index(){
        return view('pages.proveedores.index');
    }
    public function newProveedor(){
        $comunas = Comuna::all();
        return view('pages.proveedores.create', ['comunas' => $comunas]);
    }

    public function editProveedor($id){
        $proveedor = Proveedor::find($id);
        $comunas = Comuna::all();
        return view('pages.proveedores.edit', ['proveedor' => $proveedor, 'comunas' => $comunas]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = Proveedor::with('comuna');
        return DataTables::eloquent($data)->toJson();
    }

    public function getById(Request $request, $id){
        try{
            $user = Proveedor::with('comuna')->findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function getProductosProveedorById(Request $request, $id){
        try{
            $user = Proveedor::with('comuna', 'productos')->findOrFail($id);
            return response()->json($user->productos);
        }catch(Exception $ex){
            return $ex;
        }
    }
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'rut' => 'required|unique:proveedors',
                'razon_social' => 'required',
                'giro' => 'required',
                'direccion' => 'required',
                'comuna' => 'required|exists:comunas,id',
                'telefono' => '',
                'correo_contacto' => '',
                'web' => ''
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $proveedor = new Proveedor();
            $proveedor->rut = substr($request->rut, 0, -1).'-'.$request->rut[strlen($request->rut)-1];
            $proveedor->razon_social = $request->razon_social;
            $proveedor->giro = $request->giro;
            $proveedor->direccion = $request->direccion;
            $proveedor->comuna_id = $request->comuna;
            $proveedor->telefono = $request->telefono;
            $proveedor->email = $request->correo_contacto;
            $proveedor->web = $request->web;
            $proveedor->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $proveedor
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function update(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'rut' => 'required',
                'razon_social' => 'required',
                'giro' => 'required',
                'direccion' => 'required',
                'comuna' => 'required|exists:comunas,id',
                'telefono' => '',
                'correo_contacto' => '',
                'web' => ''
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }

            $proveedor = Proveedor::findOrFail($id);
            $proveedor->rut = $request->rut;
            $proveedor->razon_social = $request->razon_social;
            $proveedor->giro = $request->giro;
            $proveedor->direccion = $request->direccion;
            $proveedor->comuna_id = $request->comuna;
            $proveedor->telefono = $request->telefono;
            $proveedor->email = $request->correo_contacto;
            $proveedor->web = $request->web;
            $proveedor->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información actualizada exitosamente',
                'data' => $proveedor
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function delete(Request $request, $id){
        try{
            $proveedor = Proveedor::find($id);
            $proveedor->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Información eliminada exitosamente',
                'data' => $proveedor
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }
}
