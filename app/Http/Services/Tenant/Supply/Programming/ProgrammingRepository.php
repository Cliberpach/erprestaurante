<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Models\Tenant\Supply\Programming\Programming;
use App\Models\Tenant\Supply\Programming\ProgrammingDetail;

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
}
