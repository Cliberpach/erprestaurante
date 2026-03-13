<?php

use App\Http\Controllers\Tenant\Consumable\ConsumableBrandController;
use App\Http\Controllers\Tenant\Consumable\ConsumableCategoryController;
use App\Http\Controllers\Tenant\Consumable\ConsumableController;
use App\Http\Controllers\Tenant\Consumable\ConsumablePurchaseController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "insumos"], function () {

    Route::group(["prefix" => "insumos"], function () {
        Route::get('index', [ConsumableController::class, 'index'])->name('tenant.insumos.insumos.index');
        Route::get('get-all', [ConsumableController::class, 'getAll'])->name('tenant.insumos.insumos.getAll');
        Route::post('store', [ConsumableController::class, 'store'])->name('tenant.insumos.insumos.store');
        Route::put('update/{id}', [ConsumableController::class, 'update'])->name('tenant.insumos.insumos.update');
        Route::delete('destroy/{id}', [ConsumableController::class, 'destroy'])->name('tenant.insumos.insumos.destroy');
        Route::get('excel', [ConsumableController::class, 'excel'])->name('tenant.insumos.insumos.excel');
        Route::get('pdf', [ConsumableController::class, 'pdf'])->name('tenant.insumos.insumos.pdf');
    });

    Route::group(["prefix" => "categorias"], function () {
        Route::get('index', [ConsumableCategoryController::class, 'index'])->name('tenant.insumos.categorias.index');
        Route::get('get-all', [ConsumableCategoryController::class, 'getAll'])->name('tenant.insumos.categorias.getAll');
        Route::post('store', [ConsumableCategoryController::class, 'store'])->name('tenant.insumos.categorias.store');
        Route::put('update/{id}', [ConsumableCategoryController::class, 'update'])->name('tenant.insumos.categorias.update');
        Route::delete('destroy/{id}', [ConsumableCategoryController::class, 'destroy'])->name('tenant.insumos.categorias.destroy');
    });

    Route::group(["prefix" => "marcas"], function () {
        Route::get('index', [ConsumableBrandController::class, 'index'])->name('tenant.insumos.marcas.index');
        Route::get('get-all', [ConsumableBrandController::class, 'getAll'])->name('tenant.insumos.marcas.getAll');
        Route::post('store', [ConsumableBrandController::class, 'store'])->name('tenant.insumos.marcas.store');
        Route::put('update/{id}', [ConsumableBrandController::class, 'update'])->name('tenant.insumos.marcas.update');
        Route::delete('destroy/{id}', [ConsumableBrandController::class, 'destroy'])->name('tenant.insumos.marcas.destroy');
    });

    Route::group(["prefix" => "compras"], function () {
        Route::get('index', [ConsumablePurchaseController::class, 'index'])->name('tenant.insumos.compras.index');
        Route::get('create', [ConsumablePurchaseController::class, 'create'])->name('tenant.insumos.compras.create');
        Route::get('get-all', [ConsumablePurchaseController::class, 'getAll'])->name('tenant.insumos.compras.getAll');
        Route::post('store', [ConsumablePurchaseController::class, 'store'])->name('tenant.insumos.compras.store');
        Route::put('update/{id}', [ConsumableBrandController::class, 'update'])->name('tenant.insumos.compras.update');
        Route::delete('destroy/{id}', [ConsumableBrandController::class, 'destroy'])->name('tenant.insumos.compras.destroy');
    });
});
