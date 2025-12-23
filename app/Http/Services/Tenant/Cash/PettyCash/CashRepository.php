<?php

namespace App\Http\Services\Tenant\Cash\PettyCash;

use App\Models\Tenant\Cash\PettyCash;

class CashRepository
{
    public function insertCash(array $dto): PettyCash
    {
        return PettyCash::create($dto);
    }

    public function updateCash(array $dto, int $id): PettyCash
    {
        $cash    =   PettyCash::findOrFail($id);
        $cash->update($dto);
        return $cash;
    }

    public function findCash(int $id): PettyCash
    {
        return PettyCash::findOrFail($id);
    }

    public function destroy(int $id): PettyCash
    {
        $cash    =   PettyCash::findOrFail($id);
        $cash->status   =   'ANULADO';
        $cash->save();
        return $cash;
    }

    public function searchCashAvailable($data)
    {
        $search = $data['search'] ?? null;

        $query = PettyCash::from('petty_cashes as pc')
            ->leftJoin('petty_cash_books as pcb', function ($join) {
                $join->on('pc.id', '=', 'pcb.petty_cash_id')
                    ->where('pcb.status', 'ABIERTO');
            })
            ->whereNull('pcb.id')
            ->when($search, function ($q) use ($search) {
                $q->where('pc.name', 'like', "%{$search}%");
            })
            ->select(
                'pc.id',
                'pc.name',
                'pc.status'
            )
            ->distinct()
            ->get();

        return $query;
    }

    public function setStatus(int $id, string $status)
    {
        $cash   =   PettyCash::findOrFail($id);
        $cash->status   =   $status;
        $cash->save();
    }

    public function searchCashOpen($data)
    {
        $search = $data['search'] ?? null;
        $user_id = $data['user_id'] ?? null;

        $query = PettyCash::from('petty_cashes as pc')
            ->join('petty_cash_books as pcb','pc.id', '=', 'pcb.petty_cash_id')
            ->join('users as u','pcb.user_id', '=', 'u.id')
            ->join('shifts as s','pcb.shift_id', '=', 's.id')
            ->where('pcb.status', 'ABIERTO')
            ->when($search, function ($q) use ($search) {
                $q->where('pc.name', 'like', "%{$search}%");
            })
            ->select(
                'pc.id',
                'pc.name',
                'pc.status',
                'pcb.id as petty_cash_book_id',
                'u.name as user_name',
                'pcb.initial_date',
                's.time as shift_name'
            )
            ->distinct();

        if($user_id) {
            $query->where('pcb.user_id', $user_id);
        }

        return $query->get();
    }
}
