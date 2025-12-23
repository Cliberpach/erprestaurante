<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Models\Tenant\Supply\Programming\Programming;

class ProgrammingService
{
    private ProgrammingRepository $s_repository;
    private ProgrammingDto $s_dto;
    private ProgrammingValidation $s_validation;

    public function __construct()
    {
        $this->s_repository =   new ProgrammingRepository();
        $this->s_dto        =   new ProgrammingDto();
        $this->s_validation =   new ProgrammingValidation();
    }

    public function store(array $data): Programming
    {
        $data   =   $this->s_validation->validationStore($data);
        $dto    =   $this->s_dto->getDtoStore($data);
        $item   =   $this->s_repository->insert($dto);

        $dto    =   $this->s_dto->getDtoDetail($data, $item);
        $this->s_repository->insertDetail($dto);

        return $item;
    }

    public function update(array $data, int $id): Programming
    {
        $dto            =   $this->s_dto->getDtoUpdate($data, $id);

        $dish_preview   =   $this->s_repository->find($id);
        $item           =   $this->s_repository->update($dto, $id);

        return $item;
    }

    public function getOne(int $id): Programming
    {
        return $this->s_repository->find($id);
    }

    public function destroy(int $id): Programming
    {
        return $this->s_repository->destroy($id);
    }

    public function setStatus(int $id, string $status)
    {
        $this->s_repository->setStatus($id, $status);
    }
}
