<?php

namespace App\Http\Controllers;

use App\Permiso;
use App\PermisoRole;
use App\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mpdf\Tag\Q;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request){
        $rol = null;
        $permisos = null;
        if(isset($request->roleId)){
            $roleId = $request->roleId;
            $rol = Role::where('id', $roleId)->first();
            $permisos = [];
        }
        $roles = Role::all();
        return view('pages.permisos.index', ['rol' => $rol, 'roles' => $roles]);
    }

    public function getRoles(){
        $data = Role::query();
        return DataTables::eloquent($data)->toJson();
    }

    public function storeRol(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es coherente con la solicitada',
                    'error' => $validator->errors()
                ]);
            }

            $rol = new Role();
            $rol->nombre = $request->nombre;
            $rol->save();

            return response()->json([
                'success' => 'true',
                'msg' => 'Rol almacenado correctamente',
            ]);

        }catch(Exception $ex){
            Log::info($ex);
            return response()->json([
                'success' => 'false',
                'msg' => 'Hubo un error ajustando los permisos',
                'error' => $validator->errors()
            ]);
        }
    }

    public function getPermisos(Request $request, $id){
        try {
            $rol = Role::with('permisos:id')->findOrFail($id);
            $permisosArr = PermisoRole::where('role_id', $id)->pluck('permiso_id');
            $modulos = Permiso::select('modulo')->groupBy('modulo')->get()->pluck('modulo');
            $data = [
                'nombre' => $rol->nombre,
                'permisos' => [
                    'usuarios' => [0,0,0,0], // Booleano que representa el CRUD (Ver, Crear, Editar, Eliminar)
                    'proveedores' => [0,0,0,0],
                    'clientes' => [0,0,0,0],
                    'productos' => [0,0,0,0],
                    'ordenes_compra' => [0,0,0,0],
                    'facturas' => [0,0,0,0]
                 ]
            ];

            foreach($modulos as $modulo){
                $permisosBool = [0,0,0,0];
                $permisos = Permiso::whereIn('id', $permisosArr)->where('modulo', $modulo)->get();
                foreach($permisos as $permiso){
                    if(str_starts_with($permiso['tag'], 'ver')){
                        $permisosBool[0] = 1;
                    }else if(str_starts_with($permiso['tag'], 'crear')){
                        $permisosBool[1] = 1;
                    }else if(str_starts_with($permiso['tag'], 'editar')){
                        $permisosBool[2] = 1;
                    }else if(str_starts_with($permiso['tag'], 'eliminar')){
                        $permisosBool[3] = 1;
                    }
                }
                $data['permisos'][$modulo] = $permisosBool;
            }

            return $data;
        }catch(Exception $ex){
            return $ex;
        }
    }

    public function storePermisos(Request $request, $id){
        try{
            $validator = Validator::make($request->all(), [
                'modulos' => 'required',
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es coherente con la solicitada',
                    'error' => $validator->errors()
                ]);
            }

            $permisos = [];
            foreach($request->modulos as $modulo => $datos){
                $ver = $datos[0];
                $editar = $datos[1];
                $crear = $datos[2];
                $eliminar = $datos[3];
                //echo $modulo.PHP_EOL;
                $verId = ($ver==1)?Permiso::where('modulo', $modulo)->where('tag', 'like', 'ver-%')->first():false;
                $editarId = ($editar==1)?Permiso::where('modulo', $modulo)->where('tag', 'like', 'editar-%')->first():false;
                $crearId = ($crear==1)?Permiso::where('modulo', $modulo)->where('tag', 'like', 'crear-%')->first():false;
                $eliminarId = ($eliminar==1)?Permiso::where('modulo', $modulo)->where('tag', 'like', 'eliminar-%')->first():false;
                //echo (($verId!=null)?$verId->id:'').' '.(($editarId!=null)?$editarId->id:'').' '.(($crearId!=null)?$crearId->id:'').' '.(($eliminarId!=null)?$eliminarId->id:'');
                $permisos[] = (($verId!=null)?$verId->id:false);
                $permisos[] = (($editarId!=null)?$editarId->id:false);
                $permisos[] = (($crearId!=null)?$crearId->id:false);
                $permisos[] = (($eliminarId!=null)?$eliminarId->id:false);
                $permisos = array_filter($permisos);
            }

            $rol = Role::find($id);
            $rol->permisos()->sync($permisos);

            return response()->json([
                'success' => 'true',
                'msg' => 'Permisos del rol '.$rol->nombre.' ajustados correctamente',
            ]);
        }catch(Exception $ex){
            Log::info($ex);
            return response()->json([
                'success' => 'false',
                'msg' => 'Hubo un error ajustando los permisos',
                'error' => $validator->errors()
            ]);
        }
    }
}
