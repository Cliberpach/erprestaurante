<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Models\Tenant\Cash\PettyCashBook;
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

    public function getDetail(int $id)
    {
        return ProgrammingDetail::where('programming_id', $id)->get();
    }

    public function findFull(int $id)
    {
        return DB::table('programming as pr')
            ->join('petty_cash_books as pcb', 'pcb.id', 'pr.petty_cash_book_id')
            ->where('pr.id', $id)
            ->select(
                'pcb.petty_cash_name',
                'pcb.id as petty_cash_book_id',
                'pcb.creator_user_name',
                'pcb.shift_name',
                'pcb.initial_date',
                'pr.id as programming_id'
            )
            ->selectRaw("
                CONCAT('CM-', LPAD(pcb.id, 8, '0')) AS petty_cash_book_code,
                CONCAT('PR-', LPAD(pr.id, 8, '0')) AS programming_code
            ")
            ->first();
    }

    public function deleteDetail(int $id)
    {
        ProgrammingDetail::where('programming_id', $id)->delete();
    }

    public function belongsPettyCashBookActive($id)
    {
        $petty_cash_book    =   DB::table('programming as pr')
            ->join('petty_cash_books as pcb', 'pcb.id', 'pr.petty_cash_book_id')
            ->where('pr.id', $id)
            ->where('pcb.status', 'ABIERTO')
            ->select('pcb.id')
            ->get();

        if (count($petty_cash_book) > 1) {
            return false;
        }
        if (count($petty_cash_book) === 0) {
            return null;
        }

        return $petty_cash_book->first();
    }

    public function cancelDetails(int $id)
    {
        ProgrammingDetail::where('programming_id', $id)
            ->update([
                'status'        =>  'ANULADO',
                'updated_at'    =>  now(),
            ]);
    }
}
