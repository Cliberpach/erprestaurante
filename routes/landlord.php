<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandLord\ApiController;
use App\Http\Controllers\LandLord\CompanyController;
use App\Http\Controllers\LandLord\PlanController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth:web', 'verified'])->group(function () {

    Route::get('/home', function () {
        return redirect()->route('landlord.mantenimiento.empresas.index');
    });
    //Route::get('/dashboard', [ModuleController::class, 'home'])->name('landlord.home');

    Route::group(["prefix" => "mantenimiento"], function () {

        Route::group(["prefix" => "empresa"], function () {
            Route::get('index', [CompanyController::class, 'index'])->name('landlord.mantenimiento.empresas.index');
            Route::get('edit/{id}', [CompanyController::class, 'edit'])->name('landlord.mantenimiento.empresas.edit');
            Route::get('getCompanies', [CompanyController::class, 'getCompanies'])->name('landlord.mantenimiento.empresas.getCompanies');
            Route::get('registrar', [CompanyController::class, 'create'])->name('landlord.mantenimiento.empresas.create');
            Route::post('store', [CompanyController::class, 'store'])->name('landlord.mantenimiento.empresas.store');
            Route::post('resetearClave', [CompanyController::class, 'resetearClave'])->name('landlord.mantenimiento.empresas.resetearClave');
            Route::put('update/{id}', [CompanyController::class, 'update'])->name('landlord.mantenimiento.empresas.update');
            Route::delete('deleteTenant/{id}', [CompanyController::class, 'deleteTenant'])->name('landlord.mantenimiento.empresas.deleteTenant');
            Route::put('block_account/{id}', [CompanyController::class, 'blockAccount'])->name('landlord.mantenimiento.empresas.bloquearEmpresa');
        });

        Route::get('plan', [PlanController::class, 'index'])->name('landlord.mantenimiento.plan');
        Route::post('plan', [PlanController::class, 'store'])->name('landlord.mantenimiento.planes.store');
        Route::get('plan/edit/{id}', [PlanController::class, 'edit'])->name('landlord.mantenimiento.planes.edit');
        Route::put('plan/update/{id}', [PlanController::class, 'update'])->name('landlord.mantenimiento.planes.update');
        Route::get('plan/delete/{id}', [PlanController::class, 'delete'])->name('landlord.mantenimiento.planes.delete');
        Route::delete('plan/destroy/{id}', [PlanController::class, 'destroy'])->name('landlord.mantenimiento.planes.destroy');
    });
});

Route::get("landlord/ruc/{ruc}", [ApiController::class, 'apiRuc']);
Route::get("landlord/dni/{dni}", [ApiController::class, 'apiDni']);
