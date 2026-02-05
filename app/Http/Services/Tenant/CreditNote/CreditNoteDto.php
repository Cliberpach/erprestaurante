<?php

namespace App\Http\Services\Tenant\CreditNote;

use App\Models\Company;
use App\Models\Landlord\Customer;
use App\Models\Tenant\Sales\CreditNote\CreditNote;
use Carbon\Carbon;

class CreditNoteDto
{
    public function getDtoFromSale(array $data): array
    {
        $sale                           =   $data['sale'];
        $data_serie                     =   $data['serie'];
        $invoice_type                   =   $data['invoice_type'];

        $dto                            =   [];
        $dto['sale_id']                 =   $sale->id;
        $dto['petty_cash_book_id']      =   $sale->petty_cash_book_id;
        $dto['petty_cash_id']           =   $sale->petty_cash_id;
        $dto['petty_cash_name']         =   $sale->petty_cash_name;
        $dto['type_doc_affected']       =   $sale->type_sale_code;
        $dto['num_doc_affected']        =   $sale->serie . '-' . $sale->correlative;
        $dto['code_motive']             =   '01';
        $dto['description_motive']      =   mb_strtoupper(trim($data['motive']), 'UTF-8');
        $dto['type_money']              =   'PEN';
        $dto['warehouse_id']            =   $sale->warehouse_id;
        $dto['warehouse_name']          =   $sale->warehouse_name;
        $dto['type_sale_id']            =   $sale->type_sale_id;
        $dto['type_sale_code']          =   $sale->type_sale_code;
        $dto['type_sale_name']          =   $sale->type_sale_name;
        $dto['customer_id']             =   $sale->customer_id;
        $dto['customer_name']           =   $sale->customer_name;
        $dto['customer_type_document']  =   $sale->customer_type_document;
        $dto['customer_document_number'] =   $sale->customer_document_number;
        $dto['customer_document_code']  =   $sale->customer_document_code;
        $dto['customer_phone']          =   $sale->customer_phone;
        $dto['igv_percentage']          =   $sale->igv_percentage;
        $dto['subtotal']                =   $sale->subtotal;
        $dto['igv_amount']              =   $sale->igv_amount;
        $dto['total']                   =   $sale->total;
        $dto['legend']                  =   $sale->legend;
        $dto['mto_oper_taxed']          =   $sale->subtotal;
        $dto['mto_igv']                 =   $sale->igv_amount;
        $dto['total_taxes']             =   $sale->igv_amount;
        $dto['mto_imp_sale']            =   $sale->total;
        $dto['correlative']             =   $data_serie['correlative'];
        $dto['serie']                   =   $data_serie['serie'];
        $dto['type_invoice_id']         =   $invoice_type->id;
        $dto['type_invoice_name']       =   $invoice_type->name;
        $dto['type_invoice_code']       =   $invoice_type->symbol;
        return $dto;
    }

    public function getDtoDetail(array $data, int $credit_note_id): array
    {
        return array_map(function ($item) use ($credit_note_id) {
            $item['credit_note_id'] =   $credit_note_id;
            $item['created_at']     =   Carbon::now();
            unset(
                $item['id'],
                $item['sale_id'],
                $item['created_at'],
                $item['updated_at']
            );

            return $item;
        }, $data);
    }

    public function getDtoInvoicing(CreditNote $credit_note, $lst_detail): array
    {
        $dto            =   [];
        $customer_dto   =   [];
        $company        =   Company::findOrFail(1);

        //======== CUSTOMER DATA ==========
        $customer_dto['tipoDoc']     =   $credit_note->customer_document_code;
        $customer_dto['numDoc']      =   $credit_note->customer_document_number;
        $customer_dto['rznSocial']   =   $credit_note->customer_name;
        $customer_dto['telephone']   =   $credit_note->customer_phone;

        $customer                    =   Customer::findOrFail($credit_note->customer_id);
        $customer_dto['address']     =   $customer->address;
        $customer_dto['email']       =   $customer->email;

        //========= FACTURA MASTER =========
        $dto['ublVersion']      =   '2.1';
        $dto['tipoDoc']         =   $credit_note->type_invoice_code;
        $dto['serie']           =   $credit_note->serie;
        $dto['correlativo']     =   $credit_note->correlative;
        $dto['fechaEmision']    =   $credit_note->created_at;
        $dto['tipDocAfectado']  =   $credit_note->type_sale_code;
        $dto['numDocAfectado']  =   $credit_note->num_doc_affected;
        $dto['codMotivo']       =   '01'; //=== 01  ANULACIÓN DE LA OPERACIÓN , 07: DEVOLUCIÓN POR ITEM ===
        $dto['desMotivo']       =   $credit_note->description_motive;
        $dto['tipoMoneda']      =   'PEN';
        $dto['company']         =   1;

        $dto['client']          =   $customer_dto;
        $dto['mtoOperGravadas'] =   $credit_note->subtotal;
        $dto['mtoIGV']          =   $credit_note->igv_amount;
        $dto['totalImpuestos']  =   $credit_note->igv_amount;
        $dto['mtoImpVenta']     =   $credit_note->total;

        //======= DETAIL ===========
        $details =   [];
        foreach ($lst_detail as $item) {
            $_item  =   [];
            $_item['codProducto']       =   $item->item_id . '-' . $item->item_name;
            $_item['unidad']            =   'NIU';
            $_item['descripcion']       =   $item->item_name;
            $_item['cantidad']          =   $item->quantity;
            $_item['mtoValorUnitario']  =   $item->mto_valor_unitario;
            $_item['mtoValorVenta']     =   $item->mto_valor_venta;
            $_item['mtoBaseIgv']        =   $item->mto_base_igv;
            $_item['porcentajeIgv']     =   $item->porcentaje_igv;
            $_item['igv']               =   $item->igv;
            $_item['tipAfeIgv']         =   $item->tip_afe_igv;
            $_item['totalImpuestos']    =   $item->total_impuestos;
            $_item['mtoPrecioUnitario'] =   $item->mto_precio_unitario;

            $details[]   =   $_item;
        }

        $dto['details'] =   $details;
        $dto['legends'] =   $credit_note->legend;

        $dto['company'] =   $company;
        return $dto;
    }
}
