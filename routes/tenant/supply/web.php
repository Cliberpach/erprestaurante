<?php

use App\Http\Controllers\Tenant\Cash\ExitMoneyController;
use App\Http\Controllers\Tenant\Supply\DishController;
use App\Http\Controllers\Tenant\Supply\TableController;
use App\Http\Controllers\Tenant\Supply\TypeDishController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "abastecimiento"], function () {

    Route::group(["prefix" => "mesas"], function () {
        Route::get('index', [TableController::class, 'index'])->name('tenant.abastecimiento.mesas.index');
        Route::get('getTables', [TableController::class, 'getTables'])->name('tenant.abastecimiento.mesas.getTables');
        Route::get('getTable/{id}', [TableController::class, 'getTable'])->name('tenant.abastecimiento.mesas.getTable');
        Route::post('store', [TableController::class, 'store'])->name('tenant.abastecimiento.mesas.store');
        Route::put('update/{id}', [TableController::class, 'update'])->name('tenant.abastecimiento.mesas.update');
        Route::delete('destroy/{id}', [TableController::class, 'destroy'])->name('tenant.abastecimiento.mesas.destroy');
    });

    Route::group(["prefix" => "tipos_plato"], function () {
        Route::get('index', [TypeDishController::class, 'index'])->name('tenant.abastecimiento.tipos_plato.index');
        Route::get('getList', [TypeDishController::class, 'getList'])->name('tenant.abastecimiento.tipos_plato.getList');
        Route::get('getOne/{id}', [TypeDishController::class, 'getOne'])->name('tenant.abastecimiento.tipos_plato.getOne');
        Route::post('store', [TypeDishController::class, 'store'])->name('tenant.abastecimiento.tipos_plato.store');
        Route::put('update/{id}', [TypeDishController::class, 'update'])->name('tenant.abastecimiento.tipos_plato.update');
        Route::delete('destroy/{id}', [TypeDishController::class, 'destroy'])->name('tenant.abastecimiento.tipos_plato.destroy');
    });

    Route::group(["prefix" => "platos"], function () {
        Route::get('index', [DishController::class, 'index'])->name('tenant.abastecimiento.platos.index');
        Route::get('getList', [DishController::class, 'getList'])->name('tenant.abastecimiento.platos.getList');
        Route::get('create', [DishController::class, 'create'])->name('tenant.abastecimiento.platos.create');
        Route::get('getOne/{id}', [DishController::class, 'getOne'])->name('tenant.abastecimiento.platos.getOne');
        Route::post('store', [DishController::class, 'store'])->name('tenant.abastecimiento.platos.store');
        Route::get('edit/{id}', [DishController::class, 'edit'])->name('tenant.abastecimiento.platos.edit');
        Route::put('update/{id}', [DishController::class, 'update'])->name('tenant.abastecimiento.platos.update');
        Route::delete('destroy/{id}', [DishController::class, 'destroy'])->name('tenant.abastecimiento.platos.destroy');
    });

    Route::group(["prefix" => "categorias"], function () {
        Route::get('index', [ExitMoneyController::class, 'index'])->name('tenant.abastecimiento.categorias.index');
        Route::get('create', [ExitMoneyController::class, 'create'])->name('tenant.egreso.create');
        Route::get('getEgresos', [ExitMoneyController::class, 'getExitMoneys'])->name('tenant.egreso.getExitMoneys');
        Route::post('store', [ExitMoneyController::class, 'store'])->name('tenant.egreso.store');
        Route::get('pdf-one/{id}', [ExitMoneyController::class, 'showPDF'])->name('tenant.egreso.pdf');
        Route::put('update/{id}', [ExitMoneyController::class, 'updateExit'])->name('tenant.egreso.update');
        Route::get('edit/{id}', [ExitMoneyController::class, 'editExit'])->name('tenant.egreso.edit');
        Route::delete('destroy/{id}', [ExitMoneyController::class, 'destroy'])->name('tenant.egreso.destroy');
    });

    Route::group(["prefix" => "bebidas"], function () {
        Route::get('index', [ExitMoneyController::class, 'index'])->name('tenant.abastecimiento.bebidas.index');
        Route::get('create', [ExitMoneyController::class, 'create'])->name('tenant.egreso.create');
        Route::get('getEgresos', [ExitMoneyController::class, 'getExitMoneys'])->name('tenant.egreso.getExitMoneys');
        Route::post('store', [ExitMoneyController::class, 'store'])->name('tenant.egreso.store');
        Route::get('pdf-one/{id}', [ExitMoneyController::class, 'showPDF'])->name('tenant.egreso.pdf');
        Route::put('update/{id}', [ExitMoneyController::class, 'updateExit'])->name('tenant.egreso.update');
        Route::get('edit/{id}', [ExitMoneyController::class, 'editExit'])->name('tenant.egreso.edit');
        Route::delete('destroy/{id}', [ExitMoneyController::class, 'destroy'])->name('tenant.egreso.destroy');
    });

    Route::group(["prefix" => "programacion"], function () {
        Route::get('index', [ExitMoneyController::class, 'index'])->name('tenant.abastecimiento.programacion.index');
        Route::get('create', [ExitMoneyController::class, 'create'])->name('tenant.egreso.create');
        Route::get('getEgresos', [ExitMoneyController::class, 'getExitMoneys'])->name('tenant.egreso.getExitMoneys');
        Route::post('store', [ExitMoneyController::class, 'store'])->name('tenant.egreso.store');
        Route::get('pdf-one/{id}', [ExitMoneyController::class, 'showPDF'])->name('tenant.egreso.pdf');
        Route::put('update/{id}', [ExitMoneyController::class, 'updateExit'])->name('tenant.egreso.update');
        Route::get('edit/{id}', [ExitMoneyController::class, 'editExit'])->name('tenant.egreso.edit');
        Route::delete('destroy/{id}', [ExitMoneyController::class, 'destroy'])->name('tenant.egreso.destroy');
    });
});
