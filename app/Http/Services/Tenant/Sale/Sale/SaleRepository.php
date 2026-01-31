<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Tenant\Sales\Sale\SaleDish;
use App\Models\Tenant\Sales\Sale\SalePay;
use App\Models\Tenant\Sales\Sale\SaleProduct;

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
}
