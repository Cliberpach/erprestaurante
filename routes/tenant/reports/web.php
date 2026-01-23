<?php

use App\Http\Controllers\Tenant\Reports\ReportContableController;
use App\Http\Controllers\Tenant\Reports\ReportSaleController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "reportes"], function () {

    Route::group(["prefix" => "ventas"], function () {
        Route::get('venta', [ReportSaleController::class, 'index'])->name('tenant.reportes.ventas.index');
        Route::get('venta/getReporteVenta', [ReportSaleController::class, 'getReporteVenta'])->name('tenant.reportes.ventas.getReporteVenta');
        Route::get('venta/excel', [ReportSaleController::class, 'excel'])->name('tenant.reportes.ventas.excel');
        Route::get('venta/pdf', [ReportSaleController::class, 'pdf'])->name('tenant.reportes.ventas.pdf');
    });

    Route::group(["prefix" => "contable"], function () {
        Route::get('index', [ReportContableController::class, 'index'])->name('tenant.reportes.contable.index');
        Route::get('contable/getReporteContable', [ReportContableController::class, 'getReporteContable'])->name('tenant.reportes.contable.getReporteContable');
        Route::get('contable/excel', [ReportContableController::class, 'excel'])->name('tenant.reportes.contable.excel');
        Route::get('contable/pdf', [ReportContableController::class, 'pdf'])->name('tenant.reportes.contable.pdf');
    });

});
