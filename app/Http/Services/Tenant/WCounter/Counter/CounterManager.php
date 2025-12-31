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

    public function store(array $data):Order{
       return $this->s_service->store($data);
    }


}
