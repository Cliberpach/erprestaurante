<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\CompanyController;
use App\Http\Controllers\Tenant\Maintenance\BankAccountController;
use App\Http\Controllers\Tenant\Maintenance\CollaboratorController;
use App\Http\Controllers\Tenant\Maintenance\ConfigurationController;
use App\Http\Controllers\Tenant\Maintenance\PositionController;
use App\Http\Controllers\Tenant\Maintenance\RoleController;
use App\Http\Controllers\Tenant\Maintenance\UserController;
use App\Http\Controllers\Tenant\PlanController;

Route::group(["prefix" => "mantenimiento"], function () {

    Route::group(["prefix" => "cuentas"], function () {
        Route::get('index', [BankAccountController::class, 'index'])->name('tenant.mantenimiento.cuentas.index');
        Route::get('getCuentas', [BankAccountController::class, 'getBankAccounts'])->name('tenant.mantenimiento.cuentas.getBankAccounts');
        Route::post('store', [BankAccountController::class, 'store'])->name('tenant.mantenimiento.cuentas.store');
        Route::put('update/{id}', [BankAccountController::class, 'update'])->name('tenant.mantenimiento.cuentas.update');
        Route::delete('/destroy/{id}', [BankAccountController::class, 'destroy'])->name('tenant.mantenimiento.cuentas.destroy');
    });

    Route::group(["prefix" => "empresa"], function () {
        Route::get('index', [CompanyController::class, 'index'])->name('tenant.mantenimiento.empresas.index');
        Route::get('create', [CompanyController::class, 'create'])->name('tenant.mantenimiento.empresas.create');
        Route::get('edit/{id}', [CompanyController::class, 'edit'])->name('tenant.mantenimiento.empresas.edit');
        Route::put('update/{id}', [CompanyController::class, 'update'])->name('tenant.mantenimiento.empresas.update');
        Route::post('store', [CompanyController::class, 'store'])->name('tenant.mantenimiento.empresas.store');
        Route::put('updateInvoice/{id}', [CompanyController::class, 'updateInvoice'])->name('tenant.mantenimiento.empresas.updateInvoice');
        Route::post('storeNumeration', [CompanyController::class, 'storeNumeration'])->name('tenant.mantenimiento.empresas.storeNumeration');
        Route::get('getListNumeration', [CompanyController::class, 'getListNumeration'])->name('tenant.mantenimiento.empresas.getListNumeration');
    });

    Route::group(["prefix" => "cargos"], function () {
        Route::get('index', [PositionController::class, 'index'])->name('tenant.mantenimiento.cargos.index');
        Route::get('getPositions', [PositionController::class, 'getPositions'])->name('tenant.mantenimiento.cargos.getPositions');
        Route::post('store', [PositionController::class, 'store'])->name('tenant.mantenimiento.cargos.store');
        Route::put('update/{id}', [PositionController::class, 'update'])->name('tenant.mantenimiento.cargos.update');
        Route::delete('destroy/{id}', [PositionController::class, 'destroy'])->name('tenant.mantenimiento.cargos.destroy');
    });

    Route::group(["prefix" => "colaborador"], function () {
        Route::get('index', [CollaboratorController::class, 'index'])->name('tenant.mantenimiento.colaboradores.index');
        Route::get('getColaboradores', [CollaboratorController::class, 'getCollaborators'])->name('tenant.mantenimiento.colaboradores.getColaboradores');
        Route::get('edit/{id}', [CollaboratorController::class, 'edit'])->name('tenant.mantenimiento.colaboradores.edit');
        Route::put('update/{id}', [CollaboratorController::class, 'update'])->name('tenant.mantenimiento.colaboradores.update');
        Route::delete('destroy/{id}', [CollaboratorController::class, 'destroy'])->name('tenant.mantenimiento.colaboradores.destroy');
        Route::get('create', [CollaboratorController::class, 'create'])->name('tenant.mantenimiento.colaboradores.create');
        Route::post('store', [CollaboratorController::class, 'store'])->name('tenant.mantenimiento.colaboradores.store');
    });

    Route::group(["prefix" => "plan"], function () {
        Route::get('plan', [PlanController::class, 'index'])->name('tenant.mantenimiento.plan');
    });

    Route::group(["prefix" => "usuario"], function () {
        Route::get('index', [UserController::class, 'index'])->name('tenant.mantenimiento.usuario.index');
        Route::get('create', [UserController::class, 'create'])->name('tenant.mantenimiento.usuario.create');
        Route::get('getAll', [UserController::class, 'getAll'])->name('tenant.mantenimiento.usuario.getAll');
        Route::get('edit/{id}', [UserController::class, 'edit'])->name('tenant.mantenimiento.usuario.edit');
        Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('tenant.mantenimiento.usuario.destroy');
        Route::post('store', [UserController::class, 'store'])->name('tenant.mantenimiento.usuario.store');
        Route::put('update/{id}', [UserController::class, 'update'])->name('tenant.mantenimiento.usuario.update');
    });

    Route::group(['prefix' => 'roles', 'middleware' => ['auth']], function () {

        Route::get('/index', [RoleController::class, 'index'])->name('tenant.mantenimiento.roles.index');
        Route::get('/create', [RoleController::class, 'create'])->name('tenant.mantenimiento.roles.create');
        Route::post('/store', [RoleController::class, 'store'])->name('tenant.mantenimiento.roles.store');
        Route::put('/update/{id}', [RoleController::class, 'update'])->name('tenant.mantenimiento.roles.update');
        Route::get('/getAll', [RoleController::class, 'getAll'])->name('tenant.mantenimiento.roles.getAll');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('tenant.mantenimiento.roles.edit');
        Route::delete('/destroy/{id}', [RoleController::class, 'destroy'])->name('tenant.mantenimiento.roles.destroy');
    });

    Route::group(["prefix" => "configuracion"], function () {
        //========== CONFIGURACION =========
        Route::get('index', [ConfigurationController::class, 'index'])->name('tenant.mantenimiento.configuracion.index');
        Route::post('configuracion/store', [ConfigurationController::class, 'store'])->name('tenant.mantenimiento.configuracion.store');
    });
});
