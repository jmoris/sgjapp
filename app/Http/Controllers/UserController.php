<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(){
        return view('pages.usuarios.index');
    }

    public function newUser(){
        $roles = Role::all();
        return view('pages.usuarios.create', ['roles' => $roles]);
    }

    public function editUser($id){
        $user = User::find($id);
        $roles = Role::all();
        return view('pages.usuarios.edit', ['user' => $user, 'roles' => $roles]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = User::query();
        return DataTables::eloquent($data)->toJson();
    }

    public function getById(Request $request, $id){
        try{
            $user = User::findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'lastname' => 'required',
                'email' => 'required',
                'password' => 'required',
                'cargo' => 'required',
                'role' => 'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro'
                ]);
            }

            $user = new User();
            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->cargo = $request->cargo;
            $user->role_id = $request->role;
            $user->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información guardada exitosamente',
                'data' => $user
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function update(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'lastname' => 'required',
                'email' => 'required',
                'password' => '',
                'cargo' => 'required',
                'role' => 'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro'
                ]);
            }

            $user = User::findOrFail($id);
            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->email = $request->email;
            if($request->password!=null){
                $user->password = bcrypt($request->password);
            }
            $user->cargo = $request->cargo;
            $user->role_id = $request->role;
            $user->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información actualizada exitosamente',
                'data' => $user
            ]);
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function delete(Request $request, $id){
        try{
            $user = User::find($id);
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
