<?php

use App\Comuna;
use App\DomicilioContribuyente;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use SolucionTotal\CoreDTE\Sii;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:web', 'tenant'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/usuarios', [UserController::class, 'getAll']);
    Route::get('/usuarios/{id}', [UserController::class, 'getById']);
    Route::post('/usuarios', [UserController::class, 'store']);
    Route::post('/usuarios/editar/{id}', [UserController::class, 'update']);
    Route::delete('/usuarios/{id}', [UserController::class, 'delete']);

    Route::get('/proveedores', [ProveedorController::class, 'getAll']);
    Route::get('/proveedores/{id}', [ProveedorController::class, 'getById']);
    Route::get('/proveedores/productos/{id}', [ProveedorController::class, 'getProductosProveedorById']);
    Route::post('/proveedores', [ProveedorController::class, 'store']);
    Route::post('/proveedores/editar/{id}', [ProveedorController::class, 'update']);
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'delete']);


    Route::get('/productos', [ProductoController::class, 'getAll']);
    Route::get('/productos/{id}', [ProductoController::class, 'getById']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::post('/productos/editar/{id}', [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'delete']);

    Route::get('/productos', [ProductoController::class, 'getAll']);
    Route::get('/productos/{id}', [ProductoController::class, 'getById']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::post('/productos/editar/{id}', [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'delete']);
    Route::post('/productos/listaprecio', [ProductoController::class, 'addPrecioLista']);
    Route::post('/productos/precioproveedor', [ProductoController::class, 'addPrecioProveedor']);

    Route::prefix('compras')->group(function(){
        Route::get('ordenescompra', [OrdenCompraController::class, 'getAll']);
        Route::get('ordenescompra/vistaprevia/{folio}/{rev?}', [OrdenCompraController::class, 'vistaPreviaOC']);
        Route::post('ordenescompra', [OrdenCompraController::class, 'store']);
        Route::post('ordenescompra/editar/{id}', [OrdenCompraController::class, 'update']);
    });

    Route::prefix('ventas')->group(function(){
        Route::get('proyectos', [ProyectoController::class, 'getAll']);
        Route::post('proyectos', [ProyectoController::class, 'store']);
        Route::post('proyectos/editar/{id}', [ProyectoController::class, 'update']);
        Route::delete('proyectos/{id}', [ProyectoController::class, 'delete']);
    });

    Route::get('/roles', [PermissionController::class, 'getRoles']);
    Route::post('/roles', [PermissionController::class, 'storeRol']);
    Route::get('/roles/{id}/permisos', [PermissionController::class, 'getPermisos']);
    Route::post('/roles/{id}/permisos', [PermissionController::class, 'storePermisos']);


    Route::get('/unidades', [MaestroController::class, 'getUnidades']);
    Route::post('/unidades', [MaestroController::class, 'storeUnidad']);
    Route::delete('/unidades/{id}', [MaestroController::class, 'deleteUnidad']);

    Route::get('/categorias', [MaestroController::class, 'getCategorias']);
    Route::post('/categorias', [MaestroController::class, 'storeCategoria']);


    Route::get('/listaprecios', [MaestroController::class, 'getListaPrecios']);
    Route::post('/unidades', [MaestroController::class, 'storeUnidad']);
    Route::post('/categorias', [MaestroController::class, 'storeCategoria']);
    Route::post('/listaprecios', [MaestroController::class, 'storeListaPrecio']);


    Route::get('infodte/proveedores/{rut}', function($rut){
        \SolucionTotal\CoreDTE\Sii::setAmbiente(Sii::PRODUCCION);
        $firma_config = ['file' => storage_path('app/cert.p12'), 'pass'=>'Moris234'];
        $firma = App\Helpers\SII::temporalPEM();
        $cookies = \SolucionTotal\CoreDTE\Sii\Autenticacion::requestCookies($firma, '19587757-2');
        $info = Sii::getInfoContribuyente($rut, $cookies);
        $domicilio = DomicilioContribuyente::where('rut', $rut)->first();
        $data = ['DIRECCION' => '', 'COMUNA' => ''];
        if($domicilio != null){
            $comuna = Comuna::whereRaw('LOWER(comunas.nombre) = (?)', [strtolower($domicilio->comuna)])->first();
            $data = [
                'DIRECCION' => $domicilio->direccion,
                'COMUNA' => $comuna->id
            ];
        }

        return response()->json(array_merge($info, $data));
    });
});
