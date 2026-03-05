<?php

namespace App\Http\Services\Tenant\Alerts\AlertApp;

class AlertAppService
{
    private AlertAppRepository $s_repository;

    public function __construct()
    {
        $this->s_repository =   new AlertAppRepository();
    }

    public function setStatus(array $lstAlerts)
    {
        $this->s_repository->setStatus($lstAlerts);
    }
}
