<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Tenant\Sales\Sale\SaleDish;
use App\Models\Tenant\Sales\Sale\SalePay;
use App\Models\Tenant\Sales\Sale\SaleProduct;
use Illuminate\Support\Facades\DB;

class SaleRepository
{
    public function store(array $dto): Sale
    {
        return Sale::create($dto);
    }

    public function storeSaleDish(array $dto)
    {
        SaleDish::insert($dto);
    }

    public function storeSaleProduct(array $dto)
    {
        SaleProduct::insert($dto);
    }

    public function storeSalePay(array $dto)
    {
        SalePay::insert($dto);
    }

    public function getSaleProducts(int $id)
    {
        return SaleProduct::where('sale_id', $id)->get();
    }

    public function getSaleDishes(int $id)
    {
        return SaleDish::where('sale_id', $id)->get();
    }

    public function setConverted(Sale $sale_initial, Sale $sale_finish)
    {
        $sale_initial->converted_to_id      =   $sale_finish->id;
        $sale_initial->converted_to_serie   =   $sale_finish->serie . '-' . $sale_finish->correlative;
        $sale_initial->save();

        $sale_finish->converted_from_id     =   $sale_initial->id;
        $sale_finish->converted_from_serie  =   $sale_initial->serie . '-' . $sale_initial->correlative;
        $sale_finish->saveQuietly();
    }

    public function findSale(int $sale_id): Sale
    {
        return Sale::findOrFail($sale_id);
    }

    public function getDetail(int $sale_id)
    {
        $q1 = DB::table('sales_products as sp')
            ->select(
                'sp.sale_id',
                DB::raw("'PRODUCTO' as item_type"),
                'sp.product_id as item_id',
                'sp.product_name as item_name',
                'sp.quantity',
                'sp.sale_price',
                'sp.total',

                'sp.mto_valor_unitario',
                'sp.mto_valor_venta',
                'sp.mto_base_igv',
                'sp.porcentaje_igv',
                'sp.igv',
                'sp.tip_afe_igv',
                'sp.total_impuestos',
                'sp.mto_precio_unitario'
            )->where('sp.sale_id', $sale_id);


        $q2 = DB::table('sales_dishes as sd')
            ->select(
                'sd.sale_id',
                DB::raw("'PLATO' as item_type"),
                'sd.dish_id as item_id',
                'sd.dish_name as item_name',
                'sd.quantity',
                'sd.sale_price',
                'sd.total',

                'sd.mto_valor_unitario',
                'sd.mto_valor_venta',
                'sd.mto_base_igv',
                'sd.porcentaje_igv',
                'sd.igv',
                'sd.tip_afe_igv',
                'sd.total_impuestos',
                'sd.mto_precio_unitario'
            )->where('sd.sale_id', $sale_id);

        $items = $q1->unionAll($q2)->get();

        return $items;
    }

    public function saveSunatData(array $data, Sale $sale): Sale
    {
        $sale->response_success         =   $data['response_success'];
        $sale->response_error_code      =   $data['response_error_code'];
        $sale->response_error_message   =   $data['response_error_message'];
        $sale->cdr_response_id          =   $data['cdr_response_id'];
        $sale->cdr_response_code        =   $data['cdr_response_code'];
        $sale->cdr_response_description =   $data['cdr_response_description'];
        $sale->cdr_response_notes       =   $data['cdr_response_notes'];
        $sale->cdr_response_reference   =   $data['cdr_response_reference'];
        $sale->ruta_cdr                 =   $data['route_cdr'];
        $sale->ruta_xml                 =   $data['route_xml'];
        $sale->last_send_message        =   $data['message'];
        $sale->sunat_status             =   $data['sunat_status'];
        $sale->save();

        return $sale;
    }

    public function setSunatStatus(Sale $sale, string $sunat_status)
    {
        $sale->sunat_status =   $sunat_status;
        $sale->save();
    }
}
