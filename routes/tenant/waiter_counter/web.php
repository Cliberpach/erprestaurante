<?php

use App\Http\Controllers\Tenant\WaiterCounter\WCounterController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "mostrador_mesero"], function () {
    Route::group(["prefix" => "mostrador"], function () {
        Route::get('index', [WCounterController::class, 'index'])->name('tenant.mostrador_mesero.mostrador.index');
        Route::get('create/{table}', [WCounterController::class, 'create'])->name('tenant.mostrador_mesero.mostrador.create');
        Route::get('getAll', [WCounterController::class, 'getAll'])->name('tenant.mostrador_mesero.mostrador.getAll');
        Route::post('store', [WCounterController::class, 'store'])->name('tenant.mostrador_mesero.mostrador.store');
        Route::get('get-order-table/{table}', [WCounterController::class, 'getOrderTable'])->name('tenant.mostrador_mesero.mostrador.getOrderTable');
        Route::put('update/{id}', [WCounterController::class, 'update'])->name('tenant.mostrador_mesero.mostrador.update');
        Route::get('edit/{id}', [WCounterController::class, 'edit'])->name('tenant.mostrador_mesero.mostrador.edit');
        Route::post('pre-account', [WCounterController::class, 'preAccount'])->name('tenant.mostrador_mesero.mostrador.preCuenta');
        Route::get('getTablesFree', [WCounterController::class, 'getTablesFree'])->name('tenant.mostrador_mesero.mostrador.getTablesFree');
        Route::post('change-table', [WCounterController::class, 'changeTable'])->name('tenant.mostrador_mesero.mostrador.changeTable');
        Route::put('delete-order/{id}', [WCounterController::class, 'destroy'])->name('tenant.mostrador_mesero.mostrador.destroy');
        Route::put('add-pay/{id}', [WCounterController::class, 'addPay'])->name('tenant.mostrador_mesero.mostrador.addPay');
        Route::post('merge-tables', [WCounterController::class, 'mergeTables'])->name('tenant.mostrador_mesero.mostrador.mergeTables');
        Route::delete('unmerge-table/{fusion_id}', [WCounterController::class, 'unmergeTable'])->name('tenant.mostrador_mesero.mostrador.unmergeTable');
        Route::get('get-fusion-config', [WCounterController::class, 'getFusionConfig'])->name('tenant.mostrador_mesero.mostrador.getFusionConfig');
        Route::get('get-active-fusions', [WCounterController::class, 'getActiveFusions'])->name('tenant.mostrador_mesero.mostrador.getActiveFusions');
        Route::get('get-fusions-by-order/{order_id}', [WCounterController::class, 'getActiveFusionsByOrder'])->name('tenant.mostrador_mesero.mostrador.getActiveFusionsByOrder');
    });
});
