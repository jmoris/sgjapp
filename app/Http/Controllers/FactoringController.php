<?php

namespace App\Http\Controllers;

use App\AEC;
use App\Cesion;
use App\Cliente;
use App\Comuna;
use App\Factoring;
use App\Factura;
use App\Helpers\Herramientas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Tenant;
use Yajra\DataTables\Facades\DataTables;

class FactoringController extends Controller
{
    public function index()
    {
        return view('pages.factorings.index');
    }

    public function newFactoring()
    {
        $comunas = Comuna::all();
        return view('pages.factorings.create', ['comunas' => $comunas]);
    }

    public function editFactoring($id)
    {
        $comunas = Comuna::all();
        $factoring = Factoring::find($id);
        return view('pages.factorings.edit', ['factoring' => $factoring, 'comunas' => $comunas]);
    }

    public function showSelector(Request $request)
    {
        $factoring = Factoring::find($request->factoring);
        $cliente = Cliente::find($request->cliente);
        $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $request->fecha)));
        $documentos = Factura::where('cliente_id', $cliente->id)->where('estado', 'LIKE', '1%')->orderBy('folio', 'asc')->get();
        return view('pages.ventas.cesiones.selector', ['documentos' => $documentos, 'factoring' => $factoring, 'cliente' => $cliente, 'fecha' => $fecha]);
    }

    public function indexCesiones(Request $request)
    {
        $clientes = Cliente::orderBy('razon_social', 'asc')->get();
        $factoring = Factoring::orderBy('razon_social', 'asc')->get();
        return view('pages.ventas.cesiones.index', ['clientes' => $clientes, 'factoring' => $factoring]);
    }

    public function detailCesion($id)
    {
        $cesion = Cesion::with('factoring', 'cliente', 'aecs')->find($id);
        return view('pages.ventas.cesiones.detail', ['cesion' => $cesion]);
    }

    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = Factoring::with('comuna');  
        return DataTables::eloquent($data)->toJson();
    }

    public function getById(Request $request, $id){
        try{
            $user = Factoring::with('comuna')->findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function getProductosClienteById(Request $request, $id){
        try{
            $user = Factoring::with('comuna', 'productos')->findOrFail($id);
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
                'direccion' => 'required',
                'comuna' => 'required|exists:tenant.comunas,id',
                'correo_cesion' => 'required|email',
                'nombre_ejecutivo' => '',
                'correo_ejecutivo' => '',
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
                    $factoring = new Factoring();
                    $factoring->rut = substr($request->rut, 0, -1).'-'.$request->rut[strlen($request->rut)-1];
                    $factoring->razon_social = Herramientas::sanitizarString($request->razon_social);
                    $factoring->direccion = $request->direccion;
                    $factoring->comuna_id = $request->comuna;
                    $factoring->email_cesion = $request->correo_cesion;
                    $factoring->nombre_contacto = $request->nombre_ejecutivo;
                    $factoring->email_contacto = $request->correo_ejecutivo;
                    $factoring->save();
                }
                $currentTenant->makeCurrent();
            }else{
                $factoring = new Factoring();
                $factoring->rut = substr($request->rut, 0, -1).'-'.$request->rut[strlen($request->rut)-1];
                $factoring->razon_social = Herramientas::sanitizarString($request->razon_social);
                $factoring->direccion = $request->direccion;
                $factoring->comuna_id = $request->comuna;
                $factoring->email_cesion = $request->correo_cesion;
                $factoring->nombre_contacto = $request->nombre_ejecutivo;
                $factoring->email_contacto = $request->correo_ejecutivo;
                $factoring->save();
            }

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $factoring
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

            $cliente = Factoring::findOrFail($id);
            $cliente->rut = $request->rut;
            $cliente->razon_social = $request->razon_social;
            $cliente->giro = $request->giro;
            $cliente->direccion = $request->direccion;
            $cliente->comuna_id = $request->comuna;
            $cliente->email_dte = $request->correo_dte;
            $cliente->telefono = $request->telefono;
            $cliente->email = $request->correo_contacto;
            $cliente->tipo_pago = $request->tipo_pago;
            $cliente->dias_credito = $request->dias_credito;
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
            $cliente = Factoring::find($id);
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

    public function getAllCesiones(){
        $data = Cesion::with('factoring', 'cliente', 'aecs')->orderBy('fecha_cesion', 'desc')->get();
        return DataTables::of($data)->toJson();
    }

    public function storeCesion(Request $request){
        $cesion = new Cesion();
        $cesion->factoring_id = $request->factoring_id;
        $cesion->cliente_id = $request->cliente_id;
        $cesion->fecha_cesion = date('Y-m-d', strtotime(str_replace('/', '-', $request->fecha)));
        $cesion->monto_cesion = 0;
        $cesion->estado = 0;
        $cesion->save();
        $monto = 0;
        foreach($request->documentos as $doc){
            $aec = new AEC();
            $aec->cesion_id = $cesion->id;
            $aec->factura_id = $doc;
            $aec->estado = 0;
            $aec->save();
            $monto += $aec->factura->monto_total;
        }

        $cesion->monto_cesion = $monto;
        $cesion->save();


        return response()->json($cesion);
    }
}
