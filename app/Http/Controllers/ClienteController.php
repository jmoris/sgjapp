<?php

namespace App\Http\Controllers;

use App\Cliente;
use App\Comuna;
use App\Proveedor;
use App\Tenant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    public function index(){
        return view('pages.clientes.index');
    }
    public function newCliente(){
        $comunas = Comuna::all();
        return view('pages.clientes.create', ['comunas' => $comunas]);
    }

    public function editCliente($id){
        $cliente = Cliente::find($id);
        $comunas = Comuna::all();
        return view('pages.clientes.edit', ['cliente' => $cliente, 'comunas' => $comunas]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = Cliente::with('comuna');
        return DataTables::eloquent($data)->toJson();
    }

    public function getById(Request $request, $id){
        try{
            $user = Cliente::with('comuna')->findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function getProductosClienteById(Request $request, $id){
        try{
            $user = Cliente::with('comuna', 'productos')->findOrFail($id);
            return response()->json($user->productos);
        }catch(Exception $ex){
            return $ex;
        }
    }
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'rut' => 'required|unique:tenant.proveedors',
                'razon_social' => 'required',
                'giro' => 'required',
                'direccion' => 'required',
                'comuna' => 'required|exists:tenant.comunas,id',
                'correo_dte' => 'required|email',
                'telefono' => '',
                'correo_contacto' => '',
                'web' => '',
                'sincronizar' => 'required|boolean'
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro',
                    'error' => $validator->errors()
                ]);
            }
            if($request->sincronizar){
                $currentTenant = Tenant::current();
                $tenants = Tenant::all();
                foreach($tenants as $tenant){
                    Tenant::forgetCurrent();
                    $tenant->makeCurrent();
                    $cliente = new Cliente();
                    $cliente->rut = substr($request->rut, 0, -1).'-'.$request->rut[strlen($request->rut)-1];
                    $cliente->razon_social = $request->razon_social;
                    $cliente->giro = $request->giro;
                    $cliente->direccion = $request->direccion;
                    $cliente->comuna_id = $request->comuna;
                    $cliente->email_dte = $request->correo_dte;
                    $cliente->telefono = $request->telefono;
                    $cliente->email = $request->correo_contacto;
                    $cliente->web = $request->web;
                    $cliente->save();
                }
                $currentTenant->makeCurrent();
            }else{
                $cliente = new Cliente();
                $cliente->rut = substr($request->rut, 0, -1).'-'.$request->rut[strlen($request->rut)-1];
                $cliente->razon_social = $request->razon_social;
                $cliente->giro = $request->giro;
                $cliente->direccion = $request->direccion;
                $cliente->comuna_id = $request->comuna;
                $cliente->email_dte = $request->correo_dte;
                $cliente->telefono = $request->telefono;
                $cliente->email = $request->correo_contacto;
                $cliente->web = $request->web;
                $cliente->save();
            }

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $cliente
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
                'comuna' => 'required|exists:tenant.comunas,id',
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

            $cliente = Cliente::findOrFail($id);
            $cliente->rut = $request->rut;
            $cliente->razon_social = $request->razon_social;
            $cliente->giro = $request->giro;
            $cliente->direccion = $request->direccion;
            $cliente->comuna_id = $request->comuna;
            $cliente->email_dte = $request->correo_dte;
            $cliente->telefono = $request->telefono;
            $cliente->email = $request->correo_contacto;
            $cliente->web = $request->web;
            $cliente->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información actualizada exitosamente',
                'data' => $cliente
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function delete(Request $request, $id){
        try{
            $cliente = Cliente::find($id);
            $cliente->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Información eliminada exitosamente',
                'data' => $cliente
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }
}
