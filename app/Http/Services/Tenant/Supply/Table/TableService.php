<?php

namespace App\Http\Services\Tenant\Supply\Table;

use App\Models\Tenant\Supply\Table\Table;

class TableService
{
    private TableRepository $s_repository;
    private TableDto $s_dto;

    public function __construct()
    {
        $this->s_repository =   new TableRepository();
        $this->s_dto        =   new TableDto();
    }

    public function store(array $data): Table
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $cash   =   $this->s_repository->insertTable($dto);
        return $cash;
    }

    public function update(array $data, int $id): Table
    {
        $dto    =   $this->s_dto->getDtoStore($data);
        $cash   =   $this->s_repository->updateTable($dto, $id);
        return $cash;
    }

    public function getTable(int $id): Table
    {
        return $this->s_repository->findTable($id);
    }

    public function destroy(int $id): Table
    {
        return $this->s_repository->destroy($id);
    }

    public function searchCashAvailable(array $data)
    {
        $cashes = $this->s_repository->searchCashAvailable($data);
        return $cashes;
    }

    public function setStatus(int $id, string $status)
    {
        $this->s_repository->setStatus($id,$status);
    }
}
