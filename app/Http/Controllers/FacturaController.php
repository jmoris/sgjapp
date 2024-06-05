<?php

namespace App\Http\Controllers;

use App\Comuna;
use App\Factura;
use App\Helpers\Ajustes;
use App\Proveedor;
use App\Unidad;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FacturaController extends Controller
{
    public function index(){
        return view('pages.ventas.facturas.index');
    }

    public function newFactura(){
        $emisor = Ajustes::getEmisor();
        $comunas = Comuna::all();
        $proveedores = Proveedor::all(); // aqui clientes
        $unidades = Unidad::all();
        return view('pages.ventas.facturas.create', ['proveedores' => $proveedores, 'unidades' => $unidades,'comunas' => $comunas, 'emisor' => $emisor]);
    }
    /*
        DESDE AQUI HACIA ABAJO ESTARAN LAS FUNCIONES DE LA API
    */
    public function getAll(){
        $data = Factura::query();
        return DataTables::eloquent($data)->toJson();
    }
}
