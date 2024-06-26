<?php

use App\Http\Controllers\MaestroController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

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
Route::middleware(['auth:web'])->group(function () {
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
        Route::get('ordenescompra/vistaprevia/{folio}', [OrdenCompraController::class, 'vistaPreviaOC']);
        Route::post('ordenescompra', [OrdenCompraController::class, 'store']);
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
});
