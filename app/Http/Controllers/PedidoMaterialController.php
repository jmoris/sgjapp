<?php

namespace App\Http\Controllers;

use App\Comuna;
use App\Helpers\Ajustes;
use App\PedidoMaterial;
use App\Proveedor;
use App\Proyecto;
use App\Unidad;
use Illuminate\Http\Request;

class PedidoMaterialController extends Controller
{
    public function index(){
        return view('pages.compras.pedidosmateriales.index');
    }

    public function newPedido(){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $proveedores = Proveedor::all();
        $unidades = Unidad::all();
        $proyectos = Proyecto::where('estado', 0)->get();
        return view('pages.compras.pedidosmateriales.create', ['proveedores' => $proveedores, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'proyectos' => $proyectos]);
    }

    public function editPedido($id){
        $pedido = PedidoMaterial::with('proveedor')->find($id);
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $proveedores = Proveedor::all();
        $unidades = Unidad::all();
        $proyectos = Proyecto::where('estado', 0)->get();
        return view('pages.compras.pedidosmateriales.edit', ['pedido' => $pedido, 'proveedores' => $proveedores, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor, 'proyectos' => $proyectos]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = OrdenCompra::where('rev_activa', true)->where('estado', '!=', -1)->with('proveedor');
        return DataTables::eloquent($data)->toJson();
    }

    public function getById(Request $request, $id){
        try{
            $user = OrdenCompra::findOrFail($id);
            return response()->json($user);
        }catch(Exception $ex){
            return $ex;
        }
    }
}
