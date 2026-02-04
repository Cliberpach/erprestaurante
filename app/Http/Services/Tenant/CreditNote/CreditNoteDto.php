<?php

namespace App\Http\Services\Tenant\CreditNote;

class CreditNoteDto
{
    public function getDtoFromSale(array $data): array
    {
        $sale                           =   $data['sale'];
        $dto                            =   [];
        $dto['sale_id']                 =   $sale->id;
        $dto['type_doc_affected']       =   $sale->type_sale_code;
        $dto['num_doc_affected']        =   $sale->serie . '-' . $sale->correlative;
        $dto['code_motive']             =   '01';
        $dto['description_motive']      =   $data['motive'];
        $dto['type_money']              =   'PEN';
        $dto['warehouse_id']            =   $sale->id;
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

        return $dto;
    }
}
