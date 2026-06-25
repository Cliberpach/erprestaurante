<?php

namespace App\Http\Services\Tenant\CCounter\Counter;

use App\Models\Tenant\Sales\Sale\Sale;

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

    public function getWaiters(): array
    {
        return $this->s_service->getWaiters();
    }

    public function changeWaiter(int $order_id, array $data): void
    {
        $this->s_service->changeWaiter($order_id, $data);
    }
}
