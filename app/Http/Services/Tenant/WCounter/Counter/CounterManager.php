<?php

namespace App\Http\Services\Tenant\WCounter\Counter;

use App\Models\Tenant\Orders\Order;
use Illuminate\Contracts\View\View;

class CounterManager
{
    private CounterService $s_service;

    public function __construct()
    {
        $this->s_service    =   new CounterService();
    }

    public function create(int $table_id): View
    {
        return $this->s_service->create($table_id);
    }

    public function store(array $data): Order
    {
        return $this->s_service->store($data);
    }

    public function getOrderTable(int $table_id)
    {
        return $this->s_service->getOrderTable($table_id);
    }

    public function edit(int $id){
        return $this->s_service->edit($id);
    }

      public function update(int $id,array $data): Order
    {
        return $this->s_service->update($id,$data);
    }

}
