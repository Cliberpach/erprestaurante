<?php

use App\Http\Controllers\Tenant\CustomerController;
use App\Http\Controllers\Tenant\SaleController;
use App\Http\Controllers\Tenant\Sales\CreditNoteController;
use App\Http\Controllers\Tenant\Sales\PaymentConditionController;
use App\Http\Controllers\Tenant\Sales\PaymentMethodController;
use App\Http\Controllers\Tenant\Sales\SummaryController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "ventas"], function () {

    Route::group(["prefix" => "ventas"], function () {
        Route::get('index', [SaleController::class, 'index'])->name('tenant.ventas.comprobante_venta.index');
        Route::get('create', [SaleController::class, 'create'])->name('tenant.ventas.comprobante_venta.create');
        Route::get('getProductos', [SaleController::class, 'getProductos'])->name('tenant.ventas.comprobante_venta.getProductos');
        Route::get('validateStock', [SaleController::class, 'validateStock'])->name('tenant.ventas.comprobante_venta.validateStock');
        Route::post('store', [SaleController::class, 'store'])->name('tenant.ventas.comprobante_venta.store');
        Route::post('send_sunat', [SaleController::class, 'send_sunat'])->name('tenant.ventas.comprobante_venta.send_sunat');
        Route::get('getSales', [SaleController::class, 'getSales'])->name('tenant.ventas.comprobante_venta.getSales');
        Route::get('pdf_voucher/{id}/{size?}', [SaleController::class, 'pdf_voucher'])->name('tenant.ventas.comprobante_venta.pdf_voucher');
        Route::get('downloadXml/{id}', [SaleController::class, 'downloadXml'])->name('tenant.ventas.comprobante_venta.downloadXml');
        Route::get('downloadCdr/{id}', [SaleController::class, 'downloadCdr'])->name('tenant.ventas.comprobante_venta.downloadCdr');
        Route::post('convert', [SaleController::class, 'convert'])->name('tenant.ventas.comprobante_venta.convert');
        Route::post('annular', [SaleController::class, 'annular'])->name('tenant.ventas.comprobante_venta.annular');

        Route::get('comprobante-electronico', [SaleController::class, 'electronicReceipt'])->name('tenant.ventas.comprobante_electronico');
        Route::get('cotizacion', [SaleController::class, 'quotation'])->name('tenant.ventas.cotizacion');
    });

    Route::group(["prefix" => "clientes"], function () {
        Route::get('index', [CustomerController::class, 'index'])->name('tenant.ventas.clientes.index');
        Route::get('create', [CustomerController::class, 'create'])->name('tenant.ventas.clientes.create');
        Route::post('store', [CustomerController::class, 'store'])->name('tenant.ventas.clientes.store');
        Route::get('edit/{id}', [CustomerController::class, 'edit'])->name('tenant.ventas.clientes.edit');
        Route::put('update/{id}', [CustomerController::class, 'update'])->name('tenant.ventas.clientes.update');
        Route::delete('destroy/{id}', [CustomerController::class, 'destroy'])->name('tenant.ventas.clientes.destroy');
        Route::get('consult_document', [CustomerController::class, 'consult_document'])->name('tenant.ventas.clientes.consult_document');
        Route::get('getListCustomers', [CustomerController::class, 'getListCustomers'])->name('tenant.ventas.clientes.getListCustomers');
        Route::get('getAll', [CustomerController::class, 'getAll'])->name('tenant.ventas.clientes.getAll');
    });

    Route::group(["prefix" => "metodos_pago"], function () {
        //======= MÉTODOS DE PAGO =======
        Route::get('metodo_pago/index', [PaymentMethodController::class, 'index'])->name('tenant.ventas.metodos_pago.index');
        Route::post('metodo_pago/store', [PaymentMethodController::class, 'store'])->name('tenant.ventas.metodos_pago.store');
        Route::put('metodo_pago/update/{id}', [PaymentMethodController::class, 'update'])->name('tenant.ventas.metodos_pago.update');
        Route::get('metodo_pago/getPaymentMethods', [PaymentMethodController::class, 'getPaymentMethods'])->name('tenant.ventas.metodos_pago.getPaymentMethods');
        Route::get('assign-accounts/create/{id}', [PaymentMethodController::class, 'assignAccountsCreate'])->name('tenant.ventas.metodos_pago.assignAccountsCreate');
        Route::post('assign-accounts/store', [PaymentMethodController::class, 'assignAccountsStore'])->name('tenant.ventas.metodos_pago.assignAccountsStore');
    });

    Route::group(["prefix" => "notas_credito"], function () {
        Route::get('index/{sale?}', [CreditNoteController::class, 'index'])->name('tenant.ventas.notas_credito.index');
        Route::get('getAll', [CreditNoteController::class, 'getAll'])->name('tenant.ventas.notas_credito.getAll');
        Route::post('send_sunat', [CreditNoteController::class, 'sendSunat'])->name('tenant.ventas.notas_credito.send_sunat');
        Route::get('pdf-one/{id}', [CreditNoteController::class, 'pdfOne'])->name('tenant.ventas.notas_credito.pdfOne');
        Route::get('downloadXml/{id}', [CreditNoteController::class, 'downloadXml'])->name('tenant.ventas.notas_credito.downloadXml');
        Route::get('downloadCdr/{id}', [CreditNoteController::class, 'downloadCdr'])->name('tenant.ventas.notas_credito.downloadCdr');
    });

    Route::group(["prefix" => "condiciones_pago"], function () {
        Route::get('metodo_pago/index', [PaymentConditionController::class, 'index'])->name('tenant.ventas.condiciones_pago.index');
        Route::get('create', [PaymentConditionController::class, 'create'])->name('tenant.ventas.condiciones_pago.create');
        Route::get('edit/{id}', [PaymentConditionController::class, 'edit'])->name('tenant.ventas.condiciones_pago.edit');
        Route::post('store', [PaymentConditionController::class, 'store'])->name('tenant.ventas.condiciones_pago.store');
        Route::put('update/{id}', [PaymentConditionController::class, 'update'])->name('tenant.ventas.condiciones_pago.update');
        Route::get('getCondicionPago', [PaymentConditionController::class, 'getCondicionPago'])->name('tenant.ventas.condiciones_pago.getCondicionPago');
        Route::delete('destroy/{id}', [PaymentConditionController::class, 'destroy'])->name('tenant.ventas.condiciones_pago.destroy');
    });

    Route::group(['prefix' => 'resumenes'], function () {
        Route::get('/index', [SummaryController::class, 'index'])->name('tenant.ventas.resumenes.index');
        Route::get('getAll', [SummaryController::class, 'getAll'])->name('tenant.ventas.resumenes.getAll');
        Route::get('getInvoices/{fecha}', [SummaryController::class, 'getInvoices'])->name('tenant.ventas.resumenes.getInvoices');
        Route::post('store', [SummaryController::class, 'store'])->name('tenant.ventas.resumenes.store');
        Route::post('consultar', [SummaryController::class, 'consultar'])->name('tenant.ventas.resumenes.consultar');
        Route::post('enviarSunat', [SummaryController::class, 'sendSunat'])->name('tenant.ventas.resumenes.enviarSunat');
        Route::get('getXml/{resumen_id}', [SummaryController::class, 'getXml'])->name('tenant.ventas.resumenes.getXml');
        Route::get('getCdr/{resumen_id}', [SummaryController::class, 'getCdr'])->name('tenant.ventas.resumenes.getCdr');
        Route::get('show/{resumen_id}', [SummaryController::class, 'show'])->name('tenant.ventas.resumenes.show');
    });
});
