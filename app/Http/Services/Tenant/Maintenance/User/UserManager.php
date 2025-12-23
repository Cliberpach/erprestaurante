<?php

namespace App\Http\Services\Tenant\Maintenance\User;

class UserManager
{

    protected UserService $s_user;

    public function __construct() {
        $this->s_user    =   new UserService();
    }

    public function getMeserosLibres(){
        return $this->s_user->getMeserosLibres();
    }

}
