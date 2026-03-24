<?php

namespace App\Http\Services\Tenant\Sale\Summary;

use App\Models\Tenant\Maintenance\Company\Company;
use App\Models\Tenant\Sales\Summary\Summary;
use Illuminate\Support\Facades\Auth;

class SummaryDto
{
    public function getDtoStore(array $data): array
    {
        $data_correlative   =   $data['data_correlative'];

        $dto['serie'] = $data_correlative->serie;

        $dto['correlative'] = $data_correlative->correlative;

        $dto['date_invoices'] = $data['fecha_comprobantes'];

        if (isset($data['command'])) {
            $dto['creator_user_id'] =   null;
            $dto['creator_user_name'] = 'ENVÍO AUTOMÁTICO';
        } else {
            $dto['creator_user_id'] =   Auth::user()->id;
            $dto['creator_user_name']   =   Auth::user()->name;
        }
        return $dto;
    }

    public function getDtoDetail(array $lst_invoices, int $summary_id): array
    {
        $dto    =   [];
        foreach ($lst_invoices as $invoice) {
            $_item  =   [];
            $_item['summary_id']    =   $summary_id;
            $_item['sale_id']       =   $invoice->documento_id;
            $_item['serie']         =   $invoice->documento_serie;
            $_item['correlative']   =   $invoice->documento_correlativo;
            $_item['subtotal']      =   $invoice->documento_subtotal;
            $_item['igv']           =   $invoice->documento_igv;
            $_item['total']         =   $invoice->documento_total;
            $_item['percentage_igv']    =   $invoice->documento_porcentaje_igv;
            $_item['customer_name']     =   $invoice->documento_nombre_cliente;
            $_item['customer_document_number']  =   $invoice->documento_doc_cliente;
            $_item['customer_document_name']    =   $invoice->customer_type_document;
            $_item['created_at']                =   now();
            $_item['updated_at']                =   now();
            $dto[]  =   $_item;
        }
        return $dto;
    }

    public function getDtoInvoicing(Summary $summary, $lst_detail): array
    {
        $dto            =   [];
        $master_dto     =   [];
        $company        =   Company::findOrFail(1);

        //======== CUSTOMER DATA ==========
        $master_dto['fecGeneracion']      =   $summary->date_invoices;
        $master_dto['fecResumen']         =   $summary->date_invoices;
        $master_dto['correlativo']        =   $summary->correlative;

        //======= DETAIL ===========
        $details =   [];
        foreach ($lst_detail as $item) {
            $_item  =   [];
            $_item['tipoDoc']           =   '03';
            $_item['serieNro']          =   $item->serie . '-' . $item->correlative;
            $_item['estado']            =   '1';
            $_item['clienteTipo']       =   '1';
            $_item['clienteNro']        =   $item->customer_document_number;
            $_item['total']             =   $item->total;
            $_item['mtoOperGravadas']   =   $item->subtotal;
            $_item['mtoOperInafectas']  =   0;
            $_item['mtoOperExoneradas'] =   0;
            $_item['mtoOperExportacion'] =   0;
            $_item['mtoIgv']            =   $item->igv;

            $details[]   =   $_item;
        }

        $dto['details']     =   $details;
        $dto['master_dto']  =   $master_dto;
        $dto['company']     =   $company;
        return $dto;
    }
}
