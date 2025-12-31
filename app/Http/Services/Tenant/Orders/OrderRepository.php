<?php

namespace App\Http\Services\Tenant\Orders;

use App\Models\Tenant\Orders\Order;

class OrderRepository
{
    public function insertOrder(array $dto): Order
    {
        return Order::create($dto);
    }
}
