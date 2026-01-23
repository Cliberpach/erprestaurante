<?php

use App\Http\Controllers\Tenant\Consultas\ConsultasCreditosController;
use App\Http\Controllers\Tenant\Consultas\QueryReservationController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "consultas"], function () {

    Route::group(["prefix" => "creditos"], function () {
        Route::get('index', [ConsultasCreditosController::class, 'index'])->name('tenant.consultas.creditos.index');
        Route::get('creditos/data', [ConsultasCreditosController::class, 'data'])->name('tenant.consultas.creditos.data');
        Route::get('creditos/pdf', [ConsultasCreditosController::class, 'generateCreditPDF'])->name('tenant.consultas.creditos.pdf');
        Route::get('/creditos/excel', [ConsultasCreditosController::class, 'exportExcel'])->name('tenant.consultas.creditos.excel');
        Route::post('/creditos/generar-documento', [ConsultasCreditosController::class, 'generarDocumento'])->name('tenant.consultas.creditos.generar_documento');
    });


    Route::group(["prefix" => "reservas"], function () {
        Route::get('index', [QueryReservationController::class, 'index'])->name('tenant.consultas.reservas.index');
        Route::get('data', [QueryReservationController::class, 'data'])->name('tenant.consultas.reservas.data');
        Route::post('/creditos/generar-documento', [QueryReservationController::class, 'generarDocumento'])->name('tenant.consultas.reservas.generar-documento');
    });
});
