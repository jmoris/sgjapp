<?php

namespace App\Http\Controllers;

use App\Proyecto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProyectoController extends Controller
{
    public function index(){
        return view('pages.proyectos.index');
    }

    public function newProyecto(){
        return view('pages.proyectos.create');
    }

    public function editProyecto($id){
        $proyecto = Proyecto::find($id);
        return view('pages.proyectos.edit', ['proyecto' => $proyecto]);
    }

    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = Proyecto::query();
        return DataTables::eloquent($data)->toJson();
    }

    public function getById(Request $request, $id){
        try{
            $user = Proyecto::findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'nombre' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro'
                ]);
            }

            $proyecto = new Proyecto();
            $proyecto->nombre = $request->nombre;
            $proyecto->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $proyecto
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function update(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'nombre' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro'
                ]);
            }

            $proyecto = Proyecto::findOrFail($id);
            $proyecto->nombre = $request->nombre;
            $proyecto->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información actualizada exitosamente',
                'data' => $proyecto
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function delete(Request $request, $id){
        try{
            $proyecto = Proyecto::find($id);
            $proyecto->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Información eliminada exitosamente',
                'data' => $proyecto
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }
}
