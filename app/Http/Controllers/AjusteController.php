<?php

namespace App\Http\Controllers;

use App\Categoria;
use App\CategoriaDocumento;
use App\Comuna;
use App\Config;
use App\Helpers\Ajustes;
use App\Services\FacturapiService;
use App\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AjusteController extends Controller
{
    protected FacturapiService $facturapi;

    public function __construct(FacturapiService $facturapi)
    {
        $this->facturapi = $facturapi;
    }

    public function index(){
        $emisor = Ajustes::getEmisor();
        $unidades = Unidad::all();
        $categorias = Categoria::all();
        $categoriasdoc = CategoriaDocumento::all();
        return view('pages.ajustes.index', ['emisor' => $emisor, 'unidades' => $unidades, 'categorias' => $categorias, 'categoriasdoc' => $categoriasdoc]);
    }

    public function sincronizarSii(){
        $respuesta = $this->facturapi->consultarDatosTributarios();

        if(!($respuesta->success ?? false)){
            Log::warning('AjusteController::sincronizarSii - respuesta no exitosa', ['respuesta' => json_encode($respuesta)]);
            return response()->json([
                'success' => false,
                'msg' => $respuesta->msg ?? 'No se pudo sincronizar la información con el SII',
            ]);
        }

        $datos = $respuesta->data;

        $valores = [
            'emisor_razonsocial' => $datos->razon_social,
            'emisor_giro' => $datos->actividad_economica,
            'emisor_acteco' => $datos->actividad_economica_codigo,
            'emisor_direccion' => $datos->direccion,
        ];

        $comuna = Comuna::where('nombre', $datos->comuna)->first();
        if($comuna){
            $valores['emisor_comuna'] = $comuna->id;
        } else {
            Log::warning('AjusteController::sincronizarSii - comuna no encontrada localmente', ['comuna' => $datos->comuna]);
        }

        foreach($valores as $key => $value){
            Config::where('key', $key)->update(['value' => $value]);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Información tributaria sincronizada correctamente desde el SII' . (!$comuna ? " (comuna '{$datos->comuna}' no encontrada localmente, se dejó la anterior)" : ''),
            'emisor' => Ajustes::getEmisor(),
        ]);
    }
}
