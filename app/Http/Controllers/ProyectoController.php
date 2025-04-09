<?php

namespace App\Http\Controllers;

use App\AdjuntoProyecto;
use App\Factura;
use App\FacturaCompra;
use App\GuiaDespacho;
use App\NotaCredito;
use App\OrdenCompra;
use App\Proyecto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProyectoController extends Controller
{
    public function index(){
        return view('pages.proyectos.index');
    }

    public function newProyecto(){
        $proyectos = Proyecto::where('estado', 0)->whereNull('proyecto_id')->get();
        return view('pages.proyectos.create', ['proyectos' => $proyectos]);
    }

    public function editProyecto($id){
        $proyecto = Proyecto::find($id);
        return view('pages.proyectos.edit', ['proyecto' => $proyecto]);
    }

    public function detailProyecto($id){
        $proyecto = Proyecto::find($id);
        $facturas = Factura::where('proyecto_id', $proyecto->id)->with('cliente')->get();
        $facturascompra = FacturaCompra::where('proyecto_id', $proyecto->id)->get();
        $guias = GuiaDespacho::where('proyecto_id', $proyecto->id)->with('cliente')->get();
        $nc = NotaCredito::where('proyecto_id', $proyecto->id)->with('cliente')->get();
        $ocs = OrdenCompra::where('proyecto_id', $proyecto->id)->where('rev_activa', true)->where('estado', '!=', -1)->with('proveedor')->get();
        $total = Factura::where('proyecto_id', $proyecto->id)->sum('monto_total');
        $adjuntos = AdjuntoProyecto::where('proyecto_id', $proyecto->id)->get();
        return view('pages.proyectos.detail', [
            'proyecto' => $proyecto,
            'facturas' => $facturas,
            'facturascompra' => $facturascompra,
            'guias' => $guias,
            'notascredito' => $nc,
            'ocs' => $ocs,
            'total' => $total,
            'adjuntos' => $adjuntos]);
    }

    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = Proyecto::with('proyectopadre');
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
                'monto_proyecto' => 'required|min:0',
                'proyecto_id' => 'nullable'
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro'
                ]);
            }

            $proyecto = new Proyecto();
            $proyecto->nombre = $request->nombre;
            $proyecto->monto_proyecto = $request->monto_proyecto;
            $proyecto->proyecto_id = $request->proyecto_id;
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
                'monto_proyecto' => 'required|min:0'
            ]);

            if($validator->fails()){
                return response()->json([
                    'success' => 'false',
                    'msg' => 'La información ingresada no es suficiente para completar el registro'
                ]);
            }

            $proyecto = Proyecto::findOrFail($id);
            $proyecto->nombre = $request->nombre;
            $proyecto->monto_proyecto = $request->monto_proyecto;
            $proyecto->estado = $request->estado;
            $proyecto->save();

            return response()->json([
                'success' => true,
                'msg' => 'Información actualizada exitosamente',
                'data' => $proyecto
            ]);
        }catch(Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un problema al intentar actualizar el proyecto',
                'error' => $ex->getMessage()
            ]);
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

    public function getAllAdjuntos(Request $request, $id){
        $adjuntos = AdjuntoProyecto::where('proyecto_id', $id)->get();
        return response()->json($adjuntos);
    }

    public function uploadDocumento(Request $request, $id){
        $request->validate([
            'file' => 'required|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->store('proyectos/'.$id);

        $adjunto = new AdjuntoProyecto();
        $adjunto->proyecto_id = $id;
        $adjunto->path = $path;
        $adjunto->nombre = $file->getClientOriginalName();
        $adjunto->save();

        return response()->json([
            'success' => true,
            'msg' => 'Archivo adjuntando exitosamente al proyecto'
        ]);;
    }

    public function descargarAdjunto(Request $request, $id){
        try{
            $adjunto = AdjuntoProyecto::find($id);
            return response()->download(storage_path('app/'.$adjunto->path), $adjunto->nombre);
        }catch(Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un error al descargar el archivo adjunto',
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function deleteAdjunto(Request $request, $id){
        try{
            $adjunto = AdjuntoProyecto::find($id);
            $path = storage_path('app/'.$adjunto->path);

            unlink($path);
            $adjunto->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Archivo adjunto eliminado exitosamente'
            ]);
        }catch(Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => 'Hubo un error al intentar eliminar el archivo adjunto',
                'error' => $ex->getMessage()
            ]);
        }
    }
}
