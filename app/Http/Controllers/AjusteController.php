<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\Helpers\Ajustes;
use App\Unidad;
use Illuminate\Http\Request;

class AjusteController extends Controller
{
    public function index(){
        $emisor = Ajustes::getEmisor();
        $unidades = Unidad::all();
        $categorias = Categoria::all();
        return view('pages.ajustes.index', ['emisor' => $emisor, 'unidades' => $unidades, 'categorias' => $categorias]);
    }
}
