
<?php

use App\Http\Controllers\Tenant\Purchase\PurchaseDocumentoController;
use App\Http\Controllers\Tenant\SupplierController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "compras"], function () {

    Route::group(["prefix" => "proveedores"], function () {
        Route::get('index', [SupplierController::class, 'index'])->name('tenant.compras.proveedores.index');
        Route::get('create', [SupplierController::class, 'create'])->name('tenant.compras.proveedores.create');
        Route::delete('proveedor/destroy/{id}', [SupplierController::class, 'destroy'])->name('tenant.compras.proveedores.destroy');
        Route::get('proveedor/getSuppliers', [SupplierController::class, 'getSuppliers'])->name('tenant.compras.proveedores.getSuppliers');
        Route::get('proveedor/consultarDocumento', [SupplierController::class, 'consultarDocumento'])->name('tenant.compras.proveedores.consultarDocumento');
        Route::post('proveedor/store', [SupplierController::class, 'store'])->name('tenant.compras.proveedores.store');
        Route::get('edit/{id}', [SupplierController::class, 'edit'])->name('tenant.compras.proveedores.edit');
        Route::put('/update/{id}', [SupplierController::class, 'update'])->name('tenant.compras.proveedores.update');
        Route::get('proveedor/getLstSuppliers', [SupplierController::class, 'getLstSuppliers'])->name('tenant.compras.proveedores.getLstSuppliers');
    });


    Route::group(["prefix" => "compras"], function () {
        Route::get('index', [PurchaseDocumentoController::class, 'index'])->name('tenant.compras.documento_compra.index');
        Route::get('purchase_document/getPurchaseDocuments', [PurchaseDocumentoController::class, 'getPurchaseDocuments'])->name('tenant.compras.documento_compra.getPurchaseDocuments');
        Route::get('create', [PurchaseDocumentoController::class, 'create'])->name('tenant.compras.documento_compra.create');
        Route::get('purchase_document/getProducts', [PurchaseDocumentoController::class, 'getProducts'])->name('tenant.compras.documento_compra.getProducts');
        Route::post('purchase_document/store', [PurchaseDocumentoController::class, 'store'])->name('tenant.compras.documento_compra.store');
        Route::get('purchase_document/show/{id}', [PurchaseDocumentoController::class, 'show'])->name('tenant.compras.documento_compra.show');

        // Route::get('orden-compra', [PurchaseController::class, 'orderPurchse'])->name('tenant.compras.orden_compra');
        // Route::get('documento-compra', [PurchaseController::class, 'purchaseDocument'])->name('tenant.compras.documento_compra');
        // Route::get('gasto-diverso', [PurchaseController::class, 'miscellaneousExpenses'])->name('tenant.compras.gasto_diverso');
    });
});
