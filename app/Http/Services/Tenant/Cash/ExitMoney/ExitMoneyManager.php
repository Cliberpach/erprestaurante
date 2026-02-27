<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

class ExitMoneyManager
{
    private ExitMoneyService  $s_service;

    public function __construct()
    {
        $this->s_service    =   new ExitMoneyService();
    }

    public function store(array $data){
        return $this->s_service->store($data);
    }


}
