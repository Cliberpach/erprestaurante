<?php

namespace App\Http\Services\Tenant\Supply\Table;

use App\Models\Tenant\Supply\Table\Table;
use Illuminate\Support\Facades\DB;

class TableRepository
{
    public function insertTable(array $dto): Table
    {
        return Table::create($dto);
    }

    public function updateTable(array $dto, int $id): Table
    {
        $table    =   Table::findOrFail($id);
        $table->update($dto);
        return $table;
    }

    public function findTable(int $id): Table
    {
        return Table::findOrFail($id);
    }

    public function destroy(int $id): Table
    {
        $table           =   Table::findOrFail($id);
        $table->status   =   'ANULADO';
        $table->save();
        return $table;
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
        $item           =   Table::findOrFail($id);
        $item->status   =   $status;
        $item->save();
    }

    public function getTablesFree()
    {
        $tables =   DB::select(
            'SELECT t.*
            FROM tables t
            WHERE t.status != "ANULADO"
            WHERE NOT EXISTS (
                SELECT 1
                FROM reservations r
                WHERE r.table_id = t.id
                AND r.status = "OCUPADO"
            )'
        );

        return $tables;
    }

    public function isNotFree(int $id)
    {
        $table  =   DB::table('reservations as r')
            ->join('tables as t', 't.id', 'r.table_id')
            ->where('r.status', 'OCUPADO')
            ->where('r.table_id', $id)
            ->select('r.code', 't.name')
            ->first();

        return $table;
    }

}
