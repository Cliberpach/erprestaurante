<?php

use App\Http\Controllers\Tenant\CashierCounter\CCounterController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "mostrador_cajero"], function () {

    Route::group(["prefix" => "mostrador"], function () {
        Route::get('index', [CCounterController::class, 'index'])->name('tenant.mostrador_cajero.mostrador.index');
        Route::get('cobrar/{order}', [CCounterController::class, 'chargeCreate'])->name('tenant.mostrador_cajero.mostrador.cobrar');
        Route::get('getAll', [CCounterController::class, 'getAll'])->name('tenant.mostrador_cajero.mostrador.getAll');
        Route::post('storeInvoice', [CCounterController::class, 'storeInvoice'])->name('tenant.mostrador_cajero.mostrador.storeInvoice');
        Route::get('get-order-table/{table}', [CCounterController::class, 'getOrderTable'])->name('tenant.mostrador_cajero.mostrador.getOrderTable');
        Route::put('update/{id}', [CCounterController::class, 'update'])->name('tenant.mostrador_cajero.mostrador.update');
        Route::get('edit/{id}', [CCounterController::class, 'edit'])->name('tenant.mostrador_cajero.mostrador.edit');
        Route::get('pdf-voucher/{sale}', [CCounterController::class, 'pdfVoucher'])->name('tenant.mostrador_cajero.mostrador.pdfVoucher');
    });
});
