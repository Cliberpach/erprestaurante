<?php

namespace App\Http\Services\Tenant\CreditNote;

use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use App\Models\Tenant\DocumentSerialization;
use App\Models\Tenant\Sales\CreditNote\CreditNote;
use Exception;

class CreditNoteService
{
    private CreditNoteDto $s_dto;

    public function __construct()
    {
        $this->s_dto    =   new CreditNoteDto();
    }

    public function storeFromSale(array $data)
    {
        $invoice_type   =   $this->isActiveFromSale($data['sale']->type_sale_code);
        $correlative    =   $this->getCorrelative($invoice_type->id);dd($correlative);
        $dto            =   $this->s_dto->getDtoFromSale($data);
    }

    public function isActiveFromSale(int $type_sale_code)
    {
        $parameter  =   null;
        $symbol     =   null;
        if ($type_sale_code == '01') {
            $parameter  =   'FF';
            $symbol     =   '07';
        }
        if ($type_sale_code == '03') {
            $parameter  =   'BB';
            $symbol     =   '07';
        }

        $invoice_type   =   GeneralTableDetail::where('symbol', $symbol)->where('parameter', $parameter)->first();

        $is_active      =   DocumentSerialization::where('document_type_id', $invoice_type->id)
            ->where('company_id', 1)
            ->first();

        if (!$is_active) {
            throw new Exception($invoice_type->name . ", NO ESTÁ ACTIVO EN LA EMPRESA");
        }

        return $invoice_type;
    }

    /*
{#2181 // app\Http\Services\Tenant\Sale\Sale\SaleService.php:41
  +"correlative": "1"
  +"serie": "NV01"
}
*/
    public static function getCorrelative($type_invoice_id): object
    {
        $correlative        =   null;
        $serie              =   null;

        //======= CONTABILIZANDO SI HAY DOCUMENTOS DE VENTA EMITIDOS PARA EL TYPE SALE ======
        $cant               =   CreditNote::where('type_invoice_id',$type_invoice_id)->count();
        $serialization      =   DocumentSerialization::where('company_id', 1)->where('document_type_id', $type_invoice_id)->first();

        //==== SI LA CANT ES 0 =====
        if ($cant === 0) {
            //====== INICIAR DESDE EL STARTING NUMBER =======
            $correlative        =   $serialization->start_number;
            $serie              =   $serialization->serie;
        } else {
            //======= EN CASO YA EXISTAN DOCUMENTOS DE VENTA DEL TYPE SALE ======
            $correlative        =   $cant  +   1;
            $serie              =   $serialization->serie;
        }

        return (object)['correlative' => $correlative, 'serie' => $serie];
    }
}
