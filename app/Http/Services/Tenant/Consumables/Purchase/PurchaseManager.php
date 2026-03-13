<?php

namespace App\Http\Services\Tenant\Consumables\Purchase;

use App\Models\Tenant\Consumables\ConsumablePurchase\ConsumablePurchase;
use App\Models\Tenant\PurchaseDocument;

class PurchaseManager
{
    private PurchaseService $s_service;

    public function __construct(){
        $this->s_service    =   new PurchaseService();
    }

    public function store(array $data):ConsumablePurchase
    {
        return $this->s_service->store($data);
    }
}
