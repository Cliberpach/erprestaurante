<?php

use App\Http\Controllers\Tenant\Cash\ExitMoneyController;
use App\Http\Controllers\Tenant\Cash\PettyCashBookController;
use App\Http\Controllers\Tenant\Cash\PettyCashController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "cajas"], function () {

    Route::group(["prefix" => "cajas"], function () {
        Route::get('caja', [PettyCashController::class, 'index'])->name('tenant.cajas.cajas.index');
        Route::get('getListCash', [PettyCashController::class, 'getListCash'])->name('tenant.cajas.cajas.getListCash');
        Route::get('getCash/{id}', [PettyCashController::class, 'getCash'])->name('tenant.cajas.cajas.getCash');
        Route::post('store', [PettyCashController::class, 'store'])->name('tenant.cajas.cajas.store');
        Route::put('update/{id}', [PettyCashController::class, 'update'])->name('tenant.cajas.cajas.update');
        Route::delete('destroy/{id}', [PettyCashController::class, 'destroy'])->name('tenant.cajas.cajas.destroy');
    });

    Route::group(["prefix" => "movimientos"], function () {
        Route::get('apertura-cierre', [PettyCashBookController::class, 'index'])->name('tenant.cajas.apertura_cierre.index');
        Route::get('pdf-one', [PettyCashBookController::class, 'showPDF'])->name('tenant.cajas.apertura_cierre.pdf');
        Route::post('open-cash', [PettyCashBookController::class, 'openPettyCash'])->name('tenant.cajas.apertura_cierre.abrirCaja');
        Route::get('getCashBooks', [PettyCashBookController::class, 'getCashBooks'])->name('tenant.cajas.apertura_cierre.getCashBooks');
        Route::get('getConsolidated', [PettyCashBookController::class, 'getConsolidated'])->name('tenant.cajas.apertura_cierre.getConsolidated');
        Route::post('close-cash', [PettyCashBookController::class, 'closePettyCash'])->name('tenant.cajas.apertura_cierre.closePettyCash');
        Route::get('get-one/{id}', [PettyCashBookController::class, 'getOne'])->name('tenant.cajas.apertura_cierre.getOne');
        Route::put('update/{id}', [PettyCashBookController::class, 'update'])->name('tenant.cajas.apertura_cierre.update');
    });

    Route::group(["prefix" => "egresos"], function () {
        Route::get('index', [ExitMoneyController::class, 'index'])->name('tenant.cajas.egresos.index');
        Route::get('create', [ExitMoneyController::class, 'create'])->name('tenant.cajas.egresos.create');
        Route::get('getEgresos', [ExitMoneyController::class, 'getExitMoneys'])->name('tenant.cajas.egresos.getExitMoneys');
        Route::post('store', [ExitMoneyController::class, 'store'])->name('tenant.cajas.egresos.store');
        Route::get('pdf-one/{id}', [ExitMoneyController::class, 'showPDF'])->name('tenant.cajas.egresos.pdf');
        Route::put('update/{id}', [ExitMoneyController::class, 'updateExit'])->name('tenant.cajas.egresos.update');
        Route::get('edit/{id}', [ExitMoneyController::class, 'editExit'])->name('tenant.cajas.egresos.edit');
        Route::delete('destroy/{id}', [ExitMoneyController::class, 'destroy'])->name('tenant.cajas.egresos.destroy');

        Route::post('proveedor/guardar', [PettyCashController::class, 'supplierStore'])->middleware('verificar.caja')->name('tenant.supplier.store');
        Route::post('comprobante-pago/guardar', [PettyCashController::class, 'proofPaymentStore'])->name('tenant.proof-payment.store');
    });
});
