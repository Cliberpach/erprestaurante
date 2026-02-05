<?php

namespace App\Http\Services\Tenant\CreditNote;

use App\Models\Tenant\Sales\CreditNote\CreditNote;
use App\Models\Tenant\Sales\CreditNote\CreditNoteDish;
use App\Models\Tenant\Sales\CreditNote\CreditNoteProduct;
use Illuminate\Support\Facades\DB;

class CreditNoteRepository
{
    public function store(array $dto): CreditNote
    {
        return CreditNote::create($dto);
    }

    public function storeProducts(array $dto)
    {
        CreditNoteProduct::insert($dto);
    }

    public function storeDishes(array $dto)
    {
        CreditNoteDish::insert($dto);
    }

    public function find(int $id): CreditNote
    {
        return CreditNote::findOrFail($id);
    }

    public function getDetail(int $credit_note_id)
    {
        $q1 = DB::table('credit_notes_products as cnp')
            ->select(
                'cnp.credit_note_id',
                DB::raw("'PRODUCTO' as item_type"),
                'cnp.product_id as item_id',
                'cnp.product_name as item_name',
                'cnp.quantity',
                'cnp.sale_price',
                'cnp.total',

                'cnp.mto_valor_unitario',
                'cnp.mto_valor_venta',
                'cnp.mto_base_igv',
                'cnp.porcentaje_igv',
                'cnp.igv',
                'cnp.tip_afe_igv',
                'cnp.total_impuestos',
                'cnp.mto_precio_unitario'
            )->where('cnp.credit_note_id', $credit_note_id);


        $q2 = DB::table('credit_notes_dishes as cnd')
            ->select(
                'cnd.credit_note_id',
                DB::raw("'PLATO' as item_type"),
                'cnd.dish_id as item_id',
                'cnd.dish_name as item_name',
                'cnd.quantity',
                'cnd.sale_price',
                'cnd.total',

                'cnd.mto_valor_unitario',
                'cnd.mto_valor_venta',
                'cnd.mto_base_igv',
                'cnd.porcentaje_igv',
                'cnd.igv',
                'cnd.tip_afe_igv',
                'cnd.total_impuestos',
                'cnd.mto_precio_unitario'
            )->where('cnd.credit_note_id', $credit_note_id);

        $items = $q1->unionAll($q2)->get();

        return $items;
    }

    public function saveSunatData(array $data, CreditNote $credit_note): CreditNote
    {
        $credit_note->response_success         =   $data['response_success'];
        $credit_note->response_error_code      =   $data['response_error_code'];
        $credit_note->response_error_message   =   $data['response_error_message'];
        $credit_note->cdr_response_id          =   $data['cdr_response_id'];
        $credit_note->cdr_response_code        =   $data['cdr_response_code'];
        $credit_note->cdr_response_description =   $data['cdr_response_description'];
        $credit_note->cdr_response_notes       =   $data['cdr_response_notes'];
        $credit_note->cdr_response_reference   =   $data['cdr_response_reference'];
        $credit_note->ruta_cdr                 =   $data['route_cdr'];
        $credit_note->ruta_xml                 =   $data['route_xml'];
        $credit_note->last_send_message        =   $data['message'];
        $credit_note->sunat_status             =   $data['sunat_status'];
        $credit_note->save();

        return $credit_note;
    }
}
