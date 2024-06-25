<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Config;
use App\ListaPrecio;
use App\Unidad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function getListaPrecio(Request $request, $id){
        $lista = ListaPrecio::where('id', $id)->with('productos')->first();
        return response()->json($lista);
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

    public function storeCertificado(Request $request){
        try{
            $validated = Validator::make($request->all(), [
                'cert' => 'required',
                'password' => 'required',
            ]);
            if ($validated->fails()) {
                return response()->json([
                    'status' => 500,
                    'msg' => 'No se pudieron validar los datos',
                    'error' => $validated->errors()
                ]);
            }

            $config = Config::where('key', 'password_cert')->first();
            if($config == null){
                $config = new Config();
                $config->key = 'password_cert';
                $config->value = $request->password;
                $config->save();
            }else{
                $config->value = $request->password;
                $config->save();
            }
            $file = $request->cert;

            if($file!=null){
                $path = '/';
                if (file_exists(storage_path($path.'cert.p12'))) {
                    unlink(storage_path($path.'cert.p12'));
                }
                Storage::putFileAs($path, $file, 'cert.p12');

                $p12 = Storage::disk('local')->get('app/cert.p12');
                openssl_pkcs12_read($p12, $cert, $request->password);
                if (Storage::disk('local')->exists('app/cert.crt.pem')) {
                    Storage::disk('local')->delete('app/cert.crt.pem');
                }
                if (Storage::disk('local')->exists('app/cert.key.pem')) {
                    Storage::disk('local')->delete('app/cert.key.pem');
                }
                Storage::disk('local')->put('app/cert.crt.pem',  $cert['cert']);
                Storage::disk('local')->put('app/cert.key.pem',  $cert['pkey']);
                // CREAR CLAVE PEM YA QUE AL ACTUALIZAR EL CERTIFICADO QUEDA LA PEM ANTERIOR
            }else{
                $path = '/';
                Storage::putFileAs($path, $file, 'cert.p12');
                $p12 = Storage::disk('local')->get('app/cert.p12');
                openssl_pkcs12_read($p12, $cert, $request->password);
                if (Storage::disk('local')->exists('app/cert.crt.pem')) {
                    Storage::disk('local')->delete('app/cert.crt.pem');
                }
                Storage::disk('local')->put('app/cert.crt.pem',  $cert['cert']);
                if (Storage::disk('local')->exists('app/cert.key.pem')) {
                    Storage::disk('local')->delete('app/cert.key.pem');
                }
                Storage::disk('local')->put('app/cert.key.pem',  $cert['pkey']);
                // CREAR CLAVE PEM YA QUE AL ACTUALIZAR EL CERTIFICADO QUEDA LA PEM ANTERIOR
            }
        }catch(Exception $ex){
            return redirect()->back()->with('certificado', 'El certificado no es valido');
        }
        return response()->redirectTo('ajustes');
    }
}
