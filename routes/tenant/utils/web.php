<?php

use App\Http\Controllers\Tenant\Cash\PettyCashController;
use App\Http\Controllers\Tenant\Consumable\ConsumableController;
use App\Http\Controllers\Tenant\CustomerController;
use App\Http\Controllers\Tenant\Maintenance\BankAccountController;
use App\Http\Controllers\Tenant\Maintenance\ConfigurationController;
use App\Http\Controllers\Tenant\Maintenance\CostCenterController;
use App\Http\Controllers\Tenant\Maintenance\UserController;
use App\Http\Controllers\Tenant\Notifications\NotificationController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\SupplierController;
use App\Http\Controllers\Tenant\Supply\DishController;
use App\Http\Controllers\Tenant\WorkShop\ModelController;
use App\Http\Controllers\Tenant\WorkShop\ServiceController;
use App\Http\Controllers\Tenant\WorkShop\VehicleController;
use App\Http\Controllers\Tenant\WorkShop\YearController;
use App\Http\Controllers\UtilController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "utils"], function () {
    Route::get('cash-available-search', [PettyCashController::class, 'searchCashAvailable'])->name('tenant.utils.searchCashAvailable');

    Route::get('cash-open-search', [PettyCashController::class, 'searchCashOpen'])->name('tenant.utils.searchCashOpen');
    Route::get('get-alerts-cash', [NotificationController::class, 'getAlertsCash'])->name('tenant.utils.getAlertsCash');

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
    Route::get('get-list/free-servers', [UserController::class, 'getListFreeServers'])->name('tenant.utils.getListFreeServers');
    Route::get('search-supplier', [SupplierController::class, 'searchSupplier'])->name('tenant.utils.searchSupplier');
    Route::get('get-bank-account/{payment_method}', [BankAccountController::class, 'getBackAccountPayment'])->name('tenant.utils.getBackAccountPayment');

    Route::post('validation-password', [ConfigurationController::class, 'validationPassword'])->name('tenant.utils.validationPassword');
    Route::post('cost-center/store', [CostCenterController::class, 'storeCostCenter'])->name('tenant.utils.storeCostCenter');
    Route::get('consumables/get-list', [ConsumableController::class, 'getList'])->name('tenant.utils.getConsumables');
    Route::get('consumable-search', [ConsumableController::class, 'searchConsumable'])->name('tenant.utils.searchConsumable');
});
