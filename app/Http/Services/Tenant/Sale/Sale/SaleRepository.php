<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Models\Tenant\Sale\SaleService;
use App\Models\Tenant\SaleDetail;
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
}
