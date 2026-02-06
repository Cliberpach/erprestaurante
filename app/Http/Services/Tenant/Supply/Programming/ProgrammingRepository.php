<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Models\Tenant\Supply\Programming\Programming;
use App\Models\Tenant\Supply\Programming\ProgrammingDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgrammingRepository
{
    public function insert(array $dto): Programming
    {
        return Programming::create($dto);
    }

    public function insertDetail(array $dto)
    {
        return ProgrammingDetail::insert($dto);
    }

    public function update(array $dto, int $id): Programming
    {
        $item    =   Programming::findOrFail($id);
        $item->update($dto);
        return $item;
    }

    public function find(int $id): Programming
    {
        return Programming::findOrFail($id);
    }

    public function destroy(int $id): Programming
    {
        $item           =   Programming::findOrFail($id);
        $item->status   =   'ANULADO';
        $item->save();
        return $item;
    }

    public function setStatus(int $id, string $status)
    {
        $item           =   Programming::findOrFail($id);
        $item->status   =   $status;
        $item->save();
    }

    public function increaseStock($programming_id, $dish_id, $quantity)
    {
        DB::table('programming_detail')
            ->where('programming_id', $programming_id)
            ->where('dish_id', $dish_id)
            ->update([
                'stock' => DB::raw("stock + $quantity"),
                'updated_at' => Carbon::now(),
            ]);
    }

    public function decreaseStock($programming_id, $dish_id, $quantity)
    {
        DB::table('programming_detail')
            ->where('programming_id', $programming_id)
            ->where('dish_id', $dish_id)
            ->update([
                'stock' => DB::raw("stock - $quantity"),
                'updated_at' => Carbon::now(),
            ]);
    }

    public function increaseLstStock(array $lst_items)
    {
        foreach ($lst_items as $item) {
            $this->increaseStock($item->programming_id, $item->dish_id, $item->quantity);
        }
    }

    public function decreaseLstStock(array $lst_items)
    {
        foreach ($lst_items as $item) {
            $this->decreaseStock($item->programming_id, $item->dish_id, $item->quantity);
        }
    }
}
