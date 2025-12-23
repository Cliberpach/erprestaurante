<?php

namespace App\Http\Services\Tenant\Maintenance\User;

class UserService
{
    private UserRepository $s_repository;

    public function __construct() {
        $this->s_repository    =   new UserRepository();
    }

    public function getMeserosLibres()
    {
       return $this->s_repository->getMeserosLibres();
    }

}
