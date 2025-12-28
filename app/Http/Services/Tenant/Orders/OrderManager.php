<?php

namespace App\Http\Services\Tenant\Orders;

use Illuminate\Contracts\View\View;

class OrderManager
{
    private OrderService $s_order;

     public function __construct()
    {
        $this->s_order    =   new OrderService();
    }

    public function create(int $table_id):View{
       return $this->s_order->create($table_id);
    }


}
