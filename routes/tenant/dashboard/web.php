<?php

use App\Http\Controllers\Tenant\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;


Route::group(["prefix" => "dashboard"], function () {

    Route::group(["prefix" => "dashboard"], function () {
        Route::get('index', [DashboardController::class, 'index'])->name('tenant.dashboard.dashboard.index');
        Route::get('/getData', [DashboardController::class, 'getData'])->name('tenant.dashboard.dashboard.getData');
        Route::get('/getStockMin', [DashboardController::class, 'getStockMin'])->name('tenant.dashboard.dashboard.getStockMin');
        Route::get('/excelProductosStockMin', [DashboardController::class, 'excelProductosStockMin'])->name('tenant.dashboard.dashboard.excelProductosStockMin');

        Route::get('/excelPlatosMes', [DashboardController::class, 'excelPlatosMes'])->name('tenant.dashboard.dashboard.excelPlatosMes');
        Route::get('/excelProductosMes', [DashboardController::class, 'excelProductsMonth'])->name('tenant.dashboard.dashboard.excelProductosMes');
        Route::get('/excelMetodosPagoMes', [DashboardController::class, 'excelPaymentsMonth'])->name('tenant.dashboard.dashboard.excelMetodosPagoMes');
        Route::get('/excelCentroCostosMes', [DashboardController::class, 'excelCostCenterMonth'])->name('tenant.dashboard.dashboard.excelCentroCostosMes');
        Route::get('/excelMeserosMes', [DashboardController::class, 'excelRankingWaiterMonth'])->name('tenant.dashboard.dashboard.excelMeserosMes');

        Route::get('peak-hour-analysis', [DashboardController::class, 'peakHourAnalysis'])->name('tenant.dashboard.dashboard.peakHourAnalysis');
    });
});
