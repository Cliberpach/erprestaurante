<?php

namespace App\Http\Services\Tenant\Cash\ExitMoney;


class ExitMoneyService
{
    private ExitMoneyDto  $s_dto;
    private ExitMoneyValidation $s_validation;
    private ExitMoneyRepository $s_repository;

    public function __construct()
    {
        $this->s_dto    =   new ExitMoneyDto();
        $this->s_validation =   new ExitMoneyValidation();
        $this->s_repository =   new ExitMoneyRepository();
    }

    public function store(array $data)
    {
        $data       =   $this->s_validation->validationStore($data);
        $dto        =   $this->s_dto->getDtoStore($data);
        $instance   =   $this->s_repository->store($dto);
        dd($instance);
    }
}
