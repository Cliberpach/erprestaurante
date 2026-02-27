<?php

namespace App\Http\Services\Tenant\Supply\Programming;

use App\Http\Controllers\UtilController;
use App\Models\Tenant\Cash\PettyCashBook;
use App\Models\Tenant\Supply\Programming\Programming;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ProgrammingService
{
    private ProgrammingRepository $s_repository;
    private ProgrammingDto $s_dto;
    private ProgrammingValidation $s_validation;

    public function __construct()
    {
        $this->s_repository =   new ProgrammingRepository();
        $this->s_dto        =   new ProgrammingDto();
        $this->s_validation =   new ProgrammingValidation($this->s_repository);
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
        $instance               =   $this->s_repository->find($id);
        $data['programming']    =   $instance;
        $data                   =   $this->s_validation->validationUpdate($data, $id);

        $dto_update =   $this->s_dto->getDtoUpdate($data);
        $instance   =   $this->s_repository->update($dto_update, $id);

        $this->s_repository->deleteDetail($id);

        $dto        =   $this->s_dto->getDtoDetail($data, $instance);
        $this->s_repository->insertDetail($dto);
        return $instance;
    }

    public function getOne(int $id): Programming
    {
        return $this->s_repository->find($id);
    }

    public function destroy(int $id): Programming
    {
        $instance   =   $this->s_repository->destroy($id);
        $this->s_repository->cancelDetails($id);
        return  $instance;
    }

    public function setStatus(int $id, string $status)
    {
        $this->s_repository->setStatus($id, $status);
    }

    public function increaseLstStock(array $lst_items)
    {
        $this->s_repository->increaseLstStock($lst_items);
    }

    public function decreaseLstStock(array $lst_items)
    {
        $this->s_repository->decreaseLstStock($lst_items);
    }

    public function auto(PettyCashBook $petty_cash_book): Programming
    {
        $this->s_validation->validationAuto($petty_cash_book);
        $dto        =   $this->s_dto->getDtoAuto($petty_cash_book);
        $item       =   $this->s_repository->insert($dto);
        $dto_lst    =   $this->s_dto->getDtoLstAuto($item);
        $this->s_repository->insertDetail($dto_lst);
        return $item;
    }

    public function edit(int $id): View
    {
        $types_dish         =   UtilController::getTypesDish();
        $user               =   Auth::user();
        $roles              =   $user->getRoleNames();
        $detail             =   $this->s_repository->getDetail($id);
        $detail_formatted   =   $this->s_dto->formatLstView($detail);
        $programming        =   $this->s_repository->findFull($id);

        $vars   =   [
            'types_dish'    =>  $types_dish,
            'user'          =>  $user,
            'roles'         =>  $roles,
            'detail'        =>  $detail_formatted,
            'programming'   =>  $programming
        ];

        return view('supply.programming.edit', $vars);
    }
}
