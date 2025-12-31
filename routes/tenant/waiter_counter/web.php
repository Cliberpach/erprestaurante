<?php

use App\Http\Controllers\Tenant\Accounts\CustomerAccountController;
use App\Http\Controllers\Tenant\Accounts\SupplierAccountController;
use App\Http\Controllers\Tenant\Orders\OrderController;
use App\Http\Controllers\Tenant\WaiterCounter\WCounterController;
use Illuminate\Support\Facades\Route;


Route::group(["prefix" => "mostrador_mesero"], function () {

    Route::group(["prefix" => "mostrador"], function () {
        Route::get('index', [WCounterController::class, 'index'])->name('tenant.mostrador_mesero.mostrador.index');
        Route::get('create/{table}', [OrderController::class, 'create'])->name('tenant.mostrador_mesero.mostrador.create');
        Route::get('getAll', [WCounterController::class, 'getAll'])->name('tenant.mostrador_mesero.mostrador.getAll');
        Route::get('getCustomerAccount/{id}', [WCounterController::class, 'getCustomerAccount'])->name('tenant.cuentas.cliente.getCustomerAccount');
        Route::post('store', [WCounterController::class, 'store'])->name('tenant.mostrador_mesero.mostrador.store');
        Route::get('pdf-one/{id}', [WCounterController::class, 'pdfOne'])->name('tenant.cuentas.cliente.pdfOne');
    });
});
