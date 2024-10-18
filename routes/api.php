<?php

use App\Comuna;
use App\DomicilioContribuyente;
use App\Helpers\Herramientas;
use App\Http\Controllers\BorradorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\GuiaDespachoController;
use App\Http\Controllers\MaestroController;
use App\Http\Controllers\NotaCreditoController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\PedidoMaterialController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    Route::get('checkcomunasregiones', function(){
        $regiones = Region::all();
        foreach($regiones as $region){
            Comuna::where('id', $region->id)->update([
                'nombre' => Herramientas::sanitizarString($region->nombre),
            ]);
        }

        $comunas = Comuna::all();
        foreach($comunas as $comuna){
            Comuna::where('id', $comuna->id)->update([
                'nombre' => Herramientas::sanitizarString($comuna->nombre),
            ]);
        }

        return response()->json([
            'sucess' => true,
            'msg' => 'Comunas y regiones actualizadas'
        ]);
    });

    Route::prefix('reportes')->group(function(){
        Route::prefix('excel')->group(function(){
            Route::get('proyecto/{tipo}/{id}/{detallado?}', [ReporteController::class, 'getReporteProyecto']);
            Route::get('pedidomaterial/{id}', [ReporteController::class, 'getPedidoMaterial']);
        });
        Route::prefix('pdf')->group(function(){

        });
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

    Route::get('/clientes', [ClienteController::class, 'getAll']);
    Route::get('/clientes/{id}', [ClienteController::class, 'getById']);
    Route::get('/clientes/productos/{id}', [ClienteController::class, 'getProductosClienteById']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::post('/clientes/editar/{id}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{id}', [ClienteController::class, 'delete']);


    Route::get('/productos', [ProductoController::class, 'getAll']);
    Route::get('/productos/{id}', [ProductoController::class, 'getById']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::post('/productos/editar/{id}', [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'delete']);

    Route::get('/productos', [ProductoController::class, 'getAll']);
    Route::get('/productos/{id}', [ProductoController::class, 'getById']);
    Route::get('/productos/lista/compra', [ProductoController::class, 'getProductosCompra']);
    Route::get('/productos/lista/materiaprima', [ProductoController::class, 'getProductosMateriaPrima']);
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::post('/productos/editar/{id}', [ProductoController::class, 'update']);
    Route::delete('/productos/{id}', [ProductoController::class, 'delete']);
    Route::post('/productos/listaprecio', [ProductoController::class, 'addPrecioLista']);
    Route::post('/productos/precioproveedor', [ProductoController::class, 'addPrecioProveedor']);

    Route::prefix('borradores')->group(function(){
        Route::get('/', [BorradorController::class, 'getAll']);
        Route::get('/{id}', [BorradorController::class, 'getBorrador']);
        Route::post('/', [BorradorController::class, 'storeBorrador']);
    });

    Route::prefix('compras')->group(function(){
        Route::get('ordenescompra', [OrdenCompraController::class, 'getAll']);
        Route::get('ordenescompra/vistaprevia/{folio}/{rev?}', [OrdenCompraController::class, 'vistaPreviaOC']);
        Route::post('ordenescompra', [OrdenCompraController::class, 'store']);
        Route::post('ordenescompra/editar/{id}', [OrdenCompraController::class, 'update']);
        Route::post('ordenescompra/anular/{id}', [OrdenCompraController::class, 'delete']);

        Route::get('pedidosmateriales', [PedidoMaterialController::class, 'getAll']);
        Route::post('pedidosmateriales', [PedidoMaterialController::class, 'storePedido']);
        Route::post('pedidosmateriales/editar/{id}', [PedidoMaterialController::class, 'update']);
        Route::get('pedidosmateriales/vistaprevia/{folio}', [PedidoMaterialController::class, 'vistaPreviaPedido']);

    });

    Route::prefix('ventas')->group(function(){
        Route::get('facturas', [FacturaController::class, 'getAll']);
        Route::post('facturas', [FacturaController::class, 'storeFactura']);
        Route::get('facturas/vistaprevia/{folio}', [FacturaController::class, 'vistaPreviaFactura']);

        Route::get('guiasdespacho', [GuiaDespachoController::class, 'getAll']);
        Route::post('guiasdespacho', [GuiaDespachoController::class, 'storeGuiaDespacho']);
        Route::delete('guiasdespacho/anular/{id}', [GuiaDespachoController::class, 'anularGuiaDespacho']);
        Route::get('guiasdespacho/vistaprevia/{folio}', [GuiaDespachoController::class, 'vistaPreviaGuiaDespacho']);

        Route::get('notascredito', [NotaCreditoController::class, 'getAll']);
        Route::post('notascredito', [NotaCreditoController::class, 'storeNotaCredito']);
        Route::post('notascredito/anulacion', [NotaCreditoController::class, 'storeAnulacion']);
        Route::get('notascredito/vistaprevia/{folio}', [NotaCreditoController::class, 'vistaPreviaNotaCredito']);


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

    Route::post('/config/certificado', [MaestroController::class, 'storeCertificado']);


    Route::get('/listaprecios', [MaestroController::class, 'getListaPrecios']);
    Route::get('/listaprecios/{id}', [MaestroController::class, 'getListaPrecio']);
    Route::post('/unidades', [MaestroController::class, 'storeUnidad']);
    Route::post('/categorias', [MaestroController::class, 'storeCategoria']);
    Route::post('/listaprecios', [MaestroController::class, 'storeListaPrecio']);


    Route::get('infodte/contribuyentes/{rut}', [MaestroController::class, 'getInfoContribuyente']);
});
