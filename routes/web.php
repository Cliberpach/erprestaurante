<?php

use App\Http\Controllers\LandLord\ApiController;
use App\Http\Controllers\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\BookController;
use App\Http\Controllers\Tenant\Cash\PettyCashController;
use App\Http\Controllers\Tenant\CustomerController;
use App\Http\Controllers\Tenant\Maintenance\BankAccountController;
use App\Http\Controllers\Tenant\Maintenance\UserController as MaintenanceUserController;
use App\Http\Controllers\Tenant\ModuleController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\SupplierController;
use App\Http\Controllers\Tenant\Supply\DishController;
use App\Http\Controllers\Tenant\WorkShop\ModelController;
use App\Http\Controllers\Tenant\WorkShop\ServiceController;
use App\Http\Controllers\Tenant\WorkShop\VehicleController;
use App\Http\Controllers\Tenant\WorkShop\YearController;
use App\Http\Controllers\UtilController;

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

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    Route::get('/dashboard', [ModuleController::class, 'home'])->name('tenant.home');


    Route::group(["prefix" => "reservas"], function () {
        Route::get('reserva', [BookController::class, 'book'])->middleware('verificar.caja')->name('tenant.reservas.reserva');
        Route::get('/reserva/{id}/recibo', [BookController::class, 'showPDF'])->middleware('verificar.caja')->name('tenant.reservas.recibo');
        Route::get('/reservas/pdf', [BookController::class, 'generatePDF'])->name('tenant.reservas.pdf');
        Route::get('/available-fields', [BookController::class, 'getAvailableFields'])->name('tenat.reservas.camposdisponibles');
    });

    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'getNotificationsCount'])->name('notifications.count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/notified', [NotificationController::class, 'notified'])->name('notifications.notified');
    Route::put('/notifications/finish/{id}', [NotificationController::class, 'finish'])->name('notifications.finish');


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


    Route::get("landlord/ruc/{ruc}", [ApiController::class, 'apiRuc']);
    Route::get("landlord/dni/{dni}", [ApiController::class, 'apiDni']);

    Route::get("/logout", [ModuleController::class, 'logout'])->name('module.logout');
});

Route::group(["prefix" => "utils"], function () {
    Route::get('cash-available-search', [PettyCashController::class, 'searchCashAvailable'])->name('tenant.utils.searchCashAvailable');

    Route::get('cash-open-search', [PettyCashController::class, 'searchCashOpen'])->name('tenant.utils.searchCashOpen');

    Route::get('dish-search', [DishController::class, 'searchDish'])->name('tenant.utils.searchDish');
    Route::get('service-search', [ServiceController::class, 'searchService'])->name('tenant.utils.searchService');
    Route::get('product-search', [ProductController::class, 'searchProduct'])->name('tenant.utils.searchProduct');
    Route::get('product-search/stock', [ProductController::class, 'searchProductStock'])->name('tenant.utils.searchProductStock');
    Route::get('model-search', [ModelController::class, 'searchModel'])->name('tenant.utils.searchModel');
    Route::get('customer-search', [CustomerController::class, 'searchCustomer'])->name('tenant.utils.searchCustomer');
    Route::get('vehicle-search', [VehicleController::class, 'searchVehicle'])->name('tenant.utils.searchVehicle');
    Route::get('get-years/{model}', [YearController::class, 'getYearsModel'])->name('tenant.utils.getYearsModel');
    Route::get('serch-plate/{placa}', [VehicleController::class, 'searchPlate'])->name('tenant.utils.searchPlate');
    Route::get('validated-stock/product', [ProductController::class, 'validatedProductStock'])->name('tenant.utils.validatedProductStock');
    Route::get('validated-stock/dish', [DishController::class, 'validatedDishStock'])->name('tenant.utils.validatedDishStock');

    Route::get('getListBankAccounts', [BankAccountController::class, 'getListBankAccounts'])->name('tenant.utils.getListBankAccounts');
    Route::get('is-active-invoice/{id}', [UtilController::class, 'isActiveInvoiceType'])->name('tenant.utils.isActiveInvoiceType');

    Route::get('dishes/get-list', [DishController::class, 'getList'])->name('tenant.utils.getDisheslist');
    Route::get('dishes/get-list-programming', [DishController::class, 'getListProgramming'])->name('tenant.utils.getDishesProgramming');

    Route::get('products/get-list', [ProductController::class, 'getProducts'])->name('tenant.utils.getProducts');
    Route::get('get-list/free-servers', [MaintenanceUserController::class, 'getListFreeServers'])->name('tenant.utils.getListFreeServers');
    Route::get('search-supplier', [SupplierController::class, 'searchSupplier'])->name('tenant.utils.searchSupplier');
    Route::get('get-bank-account/{payment_method}', [BankAccountController::class, 'getBackAccountPayment'])->name('tenant.utils.getBackAccountPayment');
});
