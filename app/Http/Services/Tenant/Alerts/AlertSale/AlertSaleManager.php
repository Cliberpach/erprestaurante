<?php

namespace App\Http\Services\Tenant\Alerts\AlertSale;

class AlertSaleManager
{
    private AlertSaleService $s_service;

    public function __construct()
    {
        $this->s_service    =   new AlertSaleService();
    }

    public function store(array $data)
    {

    }
}
