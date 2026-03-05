<?php

namespace App\Http\Services\Tenant\Alerts\AlertSale;

use App\Models\Tenant\Alerts\AlertSale;
use App\Models\Tenant\Api\AlertApp;

class AlertSaleRepository
{
    public function storeMasive(array $dto)
    {
        AlertSale::insert($dto);
    }

    public function findAlertApps(array $ids)
    {
        return AlertApp::whereIn('id', $ids)->get();
    }
}
