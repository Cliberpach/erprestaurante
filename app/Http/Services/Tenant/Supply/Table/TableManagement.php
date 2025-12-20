<?php

namespace App\Http\Services\Tenant\Supply\Table;

use App\Models\Tenant\Supply\Table\Table;

class TableManagement
{
    private TableService  $s_table;

    public function __construct()
    {
        $this->s_table    =   new TableService();
    }

    public function store(array $data): Table
    {
        return $this->s_table->store($data);
    }

    public function update(array $data, int $id): Table
    {
        return $this->s_table->update($data, $id);
    }

    public function getTable(int $id): Table
    {
        return $this->s_table->getTable($id);
    }

    public function destroy(int $id): Table
    {
        return $this->s_table->destroy($id);
    }

    public function searchCashAvailable(array $data)
    {
        return $this->s_table->searchCashAvailable($data);
    }
}
