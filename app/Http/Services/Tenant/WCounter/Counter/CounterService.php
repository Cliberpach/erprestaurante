<?php

namespace App\Http\Services\Tenant\WCounter\Counter;

use App\Http\Services\Tenant\Orders\OrderService;
use App\Models\Tenant\Orders\Order;
use Illuminate\Contracts\View\View;

class CounterService
{
    private CounterValidation $s_validation;
    private OrderService $s_order;

    public function __construct()
    {
        $this->s_validation =   new CounterValidation();
        $this->s_order      =   new OrderService();
    }

    public function create(int $table): View
    {
        return $this->s_order->create($table);
    }

    public function store(array $data): Order
    {   
        $order  =   $this->s_order->store($data);
        return $order;
    }

    public function getOrderTable(int $table_id)
    {
        return $this->s_order->getOrderTable($table_id);
    }

    public function edit(int $id): View
    {
        $view   =   $this->s_order->edit($id);
        return $view;
    }

    public function update(int $id, array $data): Order
    {
        return $this->s_order->update($id, $data);
    }
}
