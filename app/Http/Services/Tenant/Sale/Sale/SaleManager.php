<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Models\Tenant\Sales\Sale\Sale;

class SaleManager
{
    protected SaleService $s_sale;

     public function __construct()
    {
        $this->s_sale    =   new SaleService();
    }

    public function store(array $data):Sale{
        return $this->s_sale->store($data);
    }

    public function convert(array $data):Sale{
        return $this->s_sale->convert($data);
    }
}
