<?php

use App\Http\Controllers\LandLord\ApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\BookController;
use App\Http\Controllers\Tenant\Maintenance\BankAccountController;
use App\Http\Controllers\Tenant\ModuleController;
use App\Http\Controllers\Tenant\Sales\QuerySaleController;
use App\Models\Tenant\Maintenance\Company\Company;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('user/tenant', [UserController::class, 'index'])->name('tenant.users.index');
Route::post('user/create', [UserController::class, 'store'])->name('tenant.users.create');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'company.status'
])->group(function () {

    Route::get('/home', [ModuleController::class, 'home'])->name('tenant.home');

    Route::group(["prefix" => "reservas"], function () {
        Route::get('reserva', [BookController::class, 'book'])->middleware('verificar.caja')->name('tenant.reservas.reserva');
        Route::get('/reserva/{id}/recibo', [BookController::class, 'showPDF'])->middleware('verificar.caja')->name('tenant.reservas.recibo');
        Route::get('/reservas/pdf', [BookController::class, 'generatePDF'])->name('tenant.reservas.pdf');
        Route::get('/available-fields', [BookController::class, 'getAvailableFields'])->name('tenat.reservas.camposdisponibles');
    });



    require __DIR__ . '/tenant/alerts/web.php';
    require __DIR__ . '/tenant/taller/web.php';
    require __DIR__ . '/tenant/mantenimiento/web.php';
    require __DIR__ . '/tenant/cash/web.php';
    require __DIR__ . '/tenant/sales/web.php';
    require __DIR__ . '/tenant/accounts/web.php';
    require __DIR__ . '/tenant/supply/web.php';
    require __DIR__ . '/tenant/waiter_counter/web.php';
    require __DIR__ . '/tenant/cashier_counter/web.php';
    require __DIR__ . '/tenant/inventory/web.php';
    require __DIR__ . '/tenant/purchases/web.php';
    require __DIR__ . '/tenant/purchases/web.php';
    require __DIR__ . '/tenant/reports/web.php';
    require __DIR__ . '/tenant/queries/web.php';
    require __DIR__ . '/tenant/dashboard/web.php';
    require __DIR__ . '/tenant/consumables/web.php';
    require __DIR__ . '/tenant/utils/web.php';


    Route::get("landlord/ruc/{ruc}", [ApiController::class, 'apiRuc']);
    Route::get("landlord/dni/{dni}", [ApiController::class, 'apiDni']);

    Route::get("/logout", [ModuleController::class, 'logout'])->name('module.logout');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/company-blocked', function () {

        $company = Company::findOrFail(1);

        if (!$company->block_account) {
            return redirect('/');
        }

        return view('tenant.errors.company-status');
    })->name('company.blocked');
});


Route::group(['prefix' => 'consultas'], function () {
    Route::get('/comprobante', [QuerySaleController::class, 'index'])->name('consultarComprobante');
    Route::post('/comprobante/buscar', [QuerySaleController::class, 'consultarComprobante'])->name('consultarComprobante.buscar');
    Route::get('/comprobante/pdf', [QuerySaleController::class, 'pdf'])->name('consultarComprobante.pdf');
    Route::get('/comprobante/xml', [QuerySaleController::class, 'xml'])->name('consultarComprobante.xml');
    Route::get('/comprobante/cdr', [QuerySaleController::class, 'cdr'])->name('consultarComprobante.cdr');
});
Route::get('consultas/{hash}', [BankAccountController::class, 'searchSale'])->name('tenant.utils.searchSale');
