<?php

namespace App\Http\Services\Tenant\Supply\TypeDish;

use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\Supply\TypeDish\TypeDish;

class TypeDishRepository
{
    public function insertTypeDish(array $dto): TypeDish
    {
        return TypeDish::create($dto);
    }

    public function updateTypeDish(array $dto, int $id): TypeDish
    {
        $item    =   TypeDish::findOrFail($id);
        $item->update($dto);
        return $item;
    }

    public function findTypeDish(int $id): TypeDish
    {
        return TypeDish::findOrFail($id);
    }

    public function destroy(int $id): TypeDish
    {
        $item           =   TypeDish::findOrFail($id);
        $item->status   =   'ANULADO';
        $item->save();
        return $item;
    }

    public function searchCashAvailable($data)
    {
        $search = $data['search'] ?? null;

        $query = Table::from('petty_cashes as pc')
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
        $cash   =   Table::findOrFail($id);
        $cash->status   =   $status;
        $cash->save();
    }
}
