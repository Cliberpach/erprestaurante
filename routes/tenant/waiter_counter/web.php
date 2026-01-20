<?php

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
        Route::get('get-order-table/{table}', [WCounterController::class, 'getOrderTable'])->name('tenant.mostrador_mesero.mostrador.getOrderTable');
        Route::put('update/{id}', [WCounterController::class, 'update'])->name('tenant.mostrador_mesero.mostrador.update');
        Route::get('edit/{id}', [WCounterController::class, 'edit'])->name('tenant.mostrador_mesero.mostrador.edit');
    });
});
