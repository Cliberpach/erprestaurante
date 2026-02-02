<?php

use App\Http\Controllers\Tenant\Reports\ReportContableController;
use App\Http\Controllers\Tenant\Reports\ReportSaleController;
use App\Http\Controllers\Tenant\Reports\RSaleDishController;
use App\Http\Controllers\Tenant\Reports\RSaleProductController;
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

    Route::group(["prefix" => "ventas_productos"], function () {
        Route::get('index', [RSaleProductController::class, 'index'])->name('tenant.reportes.ventas_productos.index');
        Route::get('getAll', [RSaleProductController::class, 'getAll'])->name('tenant.reportes.ventas_productos.getAll');
        Route::get('excel', [RSaleProductController::class, 'excel'])->name('tenant.reportes.ventas_productos.excel');
        Route::get('pdf', [RSaleProductController::class, 'pdf'])->name('tenant.reportes.ventas_productos.pdf');
    });

    Route::group(["prefix" => "ventas_platos"], function () {
        Route::get('index', [RSaleDishController::class, 'index'])->name('tenant.reportes.ventas_platos.index');
        Route::get('getAll', [RSaleDishController::class, 'getAll'])->name('tenant.reportes.ventas_platos.getAll');
        Route::get('excel', [RSaleDishController::class, 'excel'])->name('tenant.reportes.ventas_platos.excel');
        Route::get('pdf', [RSaleDishController::class, 'pdf'])->name('tenant.reportes.ventas_platos.pdf');
    });
});
