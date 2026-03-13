<?php

use App\Http\Controllers\Tenant\Consumable\ConsumableCategoryController;
use App\Http\Controllers\Tenant\Consumable\ConsumableController;
use App\Http\Controllers\Tenant\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "insumos"], function () {

    Route::group(["prefix" => "insumos"], function () {

        Route::get('index', [ConsumableController::class, 'index'])->name('tenant.insumos.insumos.index');
    });

    Route::group(["prefix" => "categorias"], function () {
        Route::get('index', [ConsumableCategoryController::class, 'index'])->name('tenant.insumos.categorias.index');
        Route::get('get-all', [ConsumableCategoryController::class, 'getAll'])->name('tenant.insumos.categorias.getAll');
        Route::post('store', [ConsumableCategoryController::class, 'store'])->name('tenant.insumos.categorias.store');
        Route::put('update/{id}', [ConsumableCategoryController::class, 'update'])->name('tenant.insumos.categorias.update');
        Route::delete('destroy/{id}', [ConsumableCategoryController::class, 'destroy'])->name('tenant.insumos.categorias.destroy');
    });
});
