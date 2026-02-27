<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

use App\Models\ExitMoney;

class ExitMoneyRepository
{

    public function store(array $dto):ExitMoney{
        return ExitMoney::create($dto);
    }

}
