<?php

namespace App\Http\Services\Tenant\CCounter\Counter;

use App\Models\Tenant\Sales\Sale\Sale;
use Illuminate\Contracts\View\View;

class CounterManager
{
    private CounterService $s_service;

    public function __construct()
    {
        $this->s_service    =   new CounterService();
    }

    public function chargeCreate(int $order)
    {
        return $this->s_service->chargeCreate($order);
    }

    public function storeInvoice(array $data):Sale
    {
        return $this->s_service->storeInvoice($data);
    }
}
