<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\DomicilioContribuyente;
use App\Http\Controllers\AjusteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\UserController;
use App\Producto;
use App\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use SolucionTotal\CoreDTE\Sii;

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'doLogin']);
Route::get('/', function(){
    return Redirect::to('/dashboard');
});


Route::middleware(['auth:web', 'tenant'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::prefix('usuarios')->middleware('tag:ver-usuario')->group(function(){
        Route::get('/', [UserController::class, 'index']);
        Route::get('/editar/{id}', [UserController::class, 'editUser']);
        Route::get('/nuevo', [UserController::class, 'newUser']);
    });

    Route::prefix('proveedores')->middleware('tag:ver-proveedor')->group(function(){
        Route::get('/', [ProveedorController::class, 'index']);
        Route::get('/editar/{id}', [ProveedorController::class, 'editProveedor']);
        Route::get('/nuevo', [ProveedorController::class, 'newProveedor']);
    });

    Route::prefix('clientes')->middleware('tag:ver-cliente')->group(function(){
        Route::get('/', [ClienteController::class, 'index']);
        Route::get('/editar/{id}', [ClienteController::class, 'editCliente']);
        Route::get('/nuevo', [ClienteController::class, 'newCliente']);
    });

    Route::prefix('productos')->middleware('tag:ver-producto')->group(function(){
        Route::get('/', [ProductoController::class, 'index']);
        Route::get('/editar/{id}', [ProductoController::class, 'editProducto']);
        Route::get('/nuevo', [ProductoController::class, 'newProducto']);
    });

    Route::prefix('compras')->group(function(){
        Route::prefix('ordenescompra')->middleware('tag:ver-orden-compra')->group(function(){
            Route::get('/', [OrdenCompraController::class, 'index']);
            Route::get('/editar/{id}', [OrdenCompraController::class, 'editOC']);
            Route::get('/nuevo', [OrdenCompraController::class, 'newOC']);
        });
    });

    Route::prefix('ventas')->group(function(){
        Route::prefix('facturas')->middleware('tag:ver-factura')->group(function(){
            Route::get('/', [FacturaController::class, 'index']);
            Route::get('/nuevo', [FacturaController::class, 'newFactura']);
        });
        Route::prefix('proyectos')->middleware('tag:ver-proyecto')->group(function(){
            Route::get('/', [ProyectoController::class, 'index']);
            Route::get('/{id}', [ProyectoController::class, 'detailProyecto'])->where('id', '[0-9]+');
            Route::get('/editar/{id}', [ProyectoController::class, 'editProyecto']);
            Route::get('/nuevo', [ProyectoController::class, 'newProyecto']);
        });
    });

    Route::prefix('permisos')->middleware('tag:ver-administracion')->group(function(){
        Route::get('/', [PermissionController::class, 'index']);
    });

    Route::prefix('ajustes')->middleware('tag:ver-administracion')->group(function(){
        Route::get('/', [AjusteController::class, 'index']);
    });

    Route::get('auth/logout', [AuthController::class, 'doLogout']);

});

//////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////


Route::group(['prefix' => 'email'], function(){
    Route::get('inbox', function () { return view('pages.email.inbox'); });
    Route::get('read', function () { return view('pages.email.read'); });
    Route::get('compose', function () { return view('pages.email.compose'); });
});

Route::group(['prefix' => 'apps'], function(){
    Route::get('chat', function () { return view('pages.apps.chat'); });
    Route::get('calendar', function () { return view('pages.apps.calendar'); });
});

Route::group(['prefix' => 'ui-components'], function(){
    Route::get('accordion', function () { return view('pages.ui-components.accordion'); });
    Route::get('alerts', function () { return view('pages.ui-components.alerts'); });
    Route::get('badges', function () { return view('pages.ui-components.badges'); });
    Route::get('breadcrumbs', function () { return view('pages.ui-components.breadcrumbs'); });
    Route::get('buttons', function () { return view('pages.ui-components.buttons'); });
    Route::get('button-group', function () { return view('pages.ui-components.button-group'); });
    Route::get('cards', function () { return view('pages.ui-components.cards'); });
    Route::get('carousel', function () { return view('pages.ui-components.carousel'); });
    Route::get('collapse', function () { return view('pages.ui-components.collapse'); });
    Route::get('dropdowns', function () { return view('pages.ui-components.dropdowns'); });
    Route::get('list-group', function () { return view('pages.ui-components.list-group'); });
    Route::get('media-object', function () { return view('pages.ui-components.media-object'); });
    Route::get('modal', function () { return view('pages.ui-components.modal'); });
    Route::get('navs', function () { return view('pages.ui-components.navs'); });
    Route::get('navbar', function () { return view('pages.ui-components.navbar'); });
    Route::get('pagination', function () { return view('pages.ui-components.pagination'); });
    Route::get('popovers', function () { return view('pages.ui-components.popovers'); });
    Route::get('progress', function () { return view('pages.ui-components.progress'); });
    Route::get('scrollbar', function () { return view('pages.ui-components.scrollbar'); });
    Route::get('scrollspy', function () { return view('pages.ui-components.scrollspy'); });
    Route::get('spinners', function () { return view('pages.ui-components.spinners'); });
    Route::get('tabs', function () { return view('pages.ui-components.tabs'); });
    Route::get('tooltips', function () { return view('pages.ui-components.tooltips'); });
});

Route::group(['prefix' => 'advanced-ui'], function(){
    Route::get('cropper', function () { return view('pages.advanced-ui.cropper'); });
    Route::get('owl-carousel', function () { return view('pages.advanced-ui.owl-carousel'); });
    Route::get('sortablejs', function () { return view('pages.advanced-ui.sortablejs'); });
    Route::get('sweet-alert', function () { return view('pages.advanced-ui.sweet-alert'); });
});

Route::group(['prefix' => 'forms'], function(){
    Route::get('basic-elements', function () { return view('pages.forms.basic-elements'); });
    Route::get('advanced-elements', function () { return view('pages.forms.advanced-elements'); });
    Route::get('editors', function () { return view('pages.forms.editors'); });
    Route::get('wizard', function () { return view('pages.forms.wizard'); });
});

Route::group(['prefix' => 'charts'], function(){
    Route::get('apex', function () { return view('pages.charts.apex'); });
    Route::get('chartjs', function () { return view('pages.charts.chartjs'); });
    Route::get('flot', function () { return view('pages.charts.flot'); });
    Route::get('peity', function () { return view('pages.charts.peity'); });
    Route::get('sparkline', function () { return view('pages.charts.sparkline'); });
});

Route::group(['prefix' => 'tables'], function(){
    Route::get('basic-tables', function () { return view('pages.tables.basic-tables'); });
    Route::get('data-table', function () { return view('pages.tables.data-table'); });
});

Route::group(['prefix' => 'icons'], function(){
    Route::get('feather-icons', function () { return view('pages.icons.feather-icons'); });
    Route::get('mdi-icons', function () { return view('pages.icons.mdi-icons'); });
});

Route::group(['prefix' => 'general'], function(){
    Route::get('blank-page', function () { return view('pages.general.blank-page'); });
    Route::get('faq', function () { return view('pages.general.faq'); });
    Route::get('invoice', function () { return view('pages.general.invoice'); });
    Route::get('profile', function () { return view('pages.general.profile'); });
    Route::get('pricing', function () { return view('pages.general.pricing'); });
    Route::get('timeline', function () { return view('pages.general.timeline'); });
});

Route::group(['prefix' => 'auth'], function(){
    Route::get('login', function () { return view('pages.auth.login'); });
    Route::get('register', function () { return view('pages.auth.register'); });
});

Route::group(['prefix' => 'error'], function(){
    Route::get('404', function () { return view('pages.error.404'); });
    Route::get('500', function () { return view('pages.error.500'); });
});

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});


// 404 for undefined routes
Route::any('/{page?}',function(){
    return View::make('pages.error.404');
})->where('page','.*');
