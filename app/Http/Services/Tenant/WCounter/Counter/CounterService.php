<?php

namespace App\Http\Services\Tenant\WCounter\Counter;

use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Http\Services\Tenant\Orders\OrderManager;
use App\Http\Services\Tenant\Orders\OrderService;
use App\Models\Tenant\Orders\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CounterService
{
    private CounterValidation $s_validation;
    private OrderService $s_order;

    public function __construct()
    {
        $this->s_validation =   new CounterValidation();
        $this->s_order      =   new OrderService();
    }

    public function store(array $data): Order
    {
        $order  =   $this->s_order->store($data);

        return $order;
    }
}
