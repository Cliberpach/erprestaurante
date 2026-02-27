<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;

use App\Models\Tenant\Cash\ExitMoney\ExitMoney;
use App\Models\Tenant\Cash\ExitMoney\ExitMoneyDetail;
use Illuminate\Support\Facades\DB;

class ExitMoneyRepository
{
    public function store(array $dto): ExitMoney
    {
        return ExitMoney::create($dto);
    }

    public function storeDetail(array $dto)
    {
        ExitMoneyDetail::insert($dto);
    }

    public function find(int $id): ?ExitMoney
    {
        return ExitMoney::findOrFail($id);
    }

    public function update(array $dto, int $id): ExitMoney
    {
        $instance    =   ExitMoney::findOrFail($id);
        $instance->update($dto);
        return $instance;
    }

    public function deleteDetail(int $id)
    {
        DB::table('exit_money_detail')->where('exit_money_id', $id)->delete();
    }

    public function delete(int $id)
    {
        $instance           =   $this->find($id);
        $instance->status   =   false;
        return $instance;
    }
}
