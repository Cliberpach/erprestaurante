<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

use App\Models\Tenant\Cash\ExitMoney\ExitMoney;

class ExitMoneyManager
{
    private ExitMoneyService  $s_service;

    public function __construct()
    {
        $this->s_service    =   new ExitMoneyService();
    }

    public function store(array $data): ExitMoney
    {
        return $this->s_service->store($data);
    }

    public function update(array $data, int $id): ExitMoney
    {
        return $this->s_service->update($data, $id);
    }

    public function destroy(int $id): ExitMoney
    {
        return $this->s_service->destroy($id);
    }
}
