
<?php

use App\Http\Controllers\Tenant\BrandController;
use App\Http\Controllers\Tenant\CategoryController;
use App\Http\Controllers\Tenant\InventoryController;
use App\Http\Controllers\Tenant\KardexController;
use App\Http\Controllers\Tenant\NoteIncomeController;
use App\Http\Controllers\Tenant\NoteReleaseController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\ValuedKardexController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "inventario"], function () {

    Route::group(["prefix" => "categorias"], function () {
        Route::get('index', [CategoryController::class, 'index'])->name('tenant.inventario.categorias.index');
        Route::get('productos/categoria/get-all', [CategoryController::class, 'getAll'])->name('tenant.inventario.categorias.get-all');
        Route::post('productos/registrar-categoria', [CategoryController::class, 'store'])->name('tenant.inventario.categorias.store');
        Route::put('productos/actualizar-categoria/{id}', [CategoryController::class, 'update'])->name('tenant.inventario.categorias.update');
        Route::delete('productos/eliminar-categoria/{id}', [CategoryController::class, 'destroy'])->name('tenant.inventario.categorias.destroy');
        Route::get('/get-format-excel', [CategoryController::class, 'getFormatExcel'])->name('tenant.inventario.categorias.get-format-excel');
        Route::post('/import-categories-excel', [CategoryController::class, 'importCategoriesExcel'])->name('tenant.inventario.categorias.import-categories-excel');
    });

    Route::group(["prefix" => "marcas"], function () {
        Route::get('index', [BrandController::class, 'index'])->name('tenant.inventario.marcas.index');
        Route::get('productos/marca/get-all', [BrandController::class, 'getAll'])->name('tenant.inventario.marcas.get-all');
        Route::post('productos/registrar-marca', [BrandController::class, 'store'])->name('tenant.inventario.marcas.store');
        Route::put('productos/actualizar-marca/{id}', [BrandController::class, 'update'])->name('tenant.inventario.marcas.update');
        Route::delete('productos/eliminar-marca/{id}', [BrandController::class, 'destroy'])->name('tenant.inventario.marcas.destroy');
        Route::get('/marca/get-format-excel', [BrandController::class, 'getFormatExcel'])->name('tenant.inventario.marcas.get-format-excel');
        Route::post('/marca/import-marcas-excel', [BrandController::class, 'importExcel'])->name('tenant.inventario.marcas.import-excel');
    });


    Route::group(["prefix" => "productos"], function () {
        Route::get('index', [ProductController::class, 'index'])->name('tenant.inventario.productos.index');
        Route::get('productos/producto/get-all', [ProductController::class, 'getAll'])->name('tenant.inventario.productos.get-all');
        Route::post('productos/registrar-producto', [ProductController::class, 'store'])->name('tenant.inventario.productos.store');
        Route::put('productos/actualizar-producto/{id}', [ProductController::class, 'update'])->name('tenant.inventario.productos.update');
        Route::delete('productos/eliminar-producto/{id}', [ProductController::class, 'destroy'])->name('tenant.inventario.productos.destroy');
        Route::get('/producto/get-format-excel', [ProductController::class, 'getFormatExcel'])->name('tenant.inventario.productos.get-format-excel');
        Route::post('/producto/import-producto-excel', [ProductController::class, 'importExcel'])->name('tenant.inventario.productos.import-excel');
        Route::post('/producto/export-producto-excel', [ProductController::class, 'exportExcel'])->name('tenant.inventario.productos.export-excel');
        Route::get('excel', [ProductController::class, 'excel'])->name('tenant.inventario.productos.excel');
        Route::get('pdf', [ProductController::class, 'pdf'])->name('tenant.inventario.productos.pdf');
    });

    Route::group(["prefix" => "inventario"], function () {
        Route::get('index', [InventoryController::class, 'index'])->name('tenant.inventario.inventario.index');
        Route::get('servicio', [InventoryController::class, 'service'])->name('tenant.inventario.inventario.servicio');
        Route::get('movimiento', [InventoryController::class, 'movement'])->name('tenant.inventario.inventario.movimiento');
        Route::get('devolucion-proveedor', [InventoryController::class, 'supplierReturn'])->name('tenant.inventario.inventario.devolucion_proveedor');
        Route::get('inventario/getInventory', [InventoryController::class, 'getInventory'])->name('tenant.inventario.inventario.getInventory');
        Route::get('inventario/excel', [InventoryController::class, 'excel'])->name('tenant.inventario.inventario.excel');
        Route::get('inventario/pdf', [InventoryController::class, 'pdf'])->name('tenant.inventario.inventario.pdf');
    });

    Route::group(["prefix" => "kardex"], function () {
        //============ KARDEX ============
        Route::get('index', [KardexController::class, 'index'])->name('tenant.inventario.kardex.index');
        Route::get('getKardex', [KardexController::class, 'getKardex'])->name('tenant.inventario.kardex.getKardex');
        Route::get('kardex/excel', [KardexController::class, 'excel'])->name('tenant.inventario.kardex.excel');
        Route::get('kardex/pdf', [KardexController::class, 'pdf'])->name('tenant.inventario.kardex.pdf');
    });

    Route::group(["prefix" => "kardex-valor"], function () {
        Route::get('index', [ValuedKardexController::class, 'index'])->name('tenant.inventario.kardex_valorizado.index');
        Route::get('kardex-valor/getValuedKardex', [ValuedKardexController::class, 'getValuedKardex'])->name('tenant.inventario.kardex_valorizado.getValuedKardex');
        Route::get('kardex-valor/pdf', [ValuedKardexController::class, 'pdf'])->name('tenant.inventario.kardex_valorizado.pdf');
    });

    Route::group(["prefix" => "nota-ingreso"], function () {
        Route::get('index', [NoteIncomeController::class, 'index'])->name('tenant.inventario.nota_ingreso.index');
        Route::get('getNoteIncome', [NoteIncomeController::class, 'getNoteIncome'])->name('tenant.inventario.nota_ingreso.getNoteIncome');
        Route::get('nota_ingreso/create', [NoteIncomeController::class, 'create'])->name('tenant.inventario.nota_ingreso.create');
        Route::post('nota_ingreso/store', [NoteIncomeController::class, 'store'])->name('tenant.inventario.nota_ingreso.store');
        Route::get('getProducts', [NoteIncomeController::class, 'getProducts'])->name('tenant.inventario.nota_ingreso.getProducts');
        Route::get('nota_ingreso/show/{id}', [NoteIncomeController::class, 'show'])->name('tenant.inventario.nota_ingreso.show');
    });


    Route::group(["prefix" => "nota-salida"], function () {
        Route::get('index', [NoteReleaseController::class, 'index'])->name('tenant.inventario.nota_salida.index');
        Route::get('nota_salida/create', [NoteReleaseController::class, 'create'])->name('tenant.inventario.nota_salida.create');
        Route::get('nota_salida/getProducts', [NoteReleaseController::class, 'getProducts'])->name('tenant.inventario.nota_salida.getProducts');
        Route::get('nota_salida/validateStock/{product_id}/{quantity}', [NoteReleaseController::class, 'validateStock'])->name('tenant.inventario.nota_salida.validateStock');
        Route::post('nota_salida/store', [NoteReleaseController::class, 'store'])->name('tenant.inventario.nota_salida.store');
        Route::get('getNotesRelease', [NoteReleaseController::class, 'getNotesRelease'])->name('tenant.inventario.nota_salida.getNotesRelease');
        Route::get('nota_salida/show/{id}', [NoteReleaseController::class, 'show'])->name('tenant.inventario.nota_salida.show');
    });
});
