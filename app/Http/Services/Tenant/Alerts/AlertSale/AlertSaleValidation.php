<?php

namespace App\Http\Services\Tenant\Alerts\AlertSale;

use Exception;

class AlertSaleValidation
{
    private AlertSaleRepository $s_repository;

    public function __construct($s_repository)
    {
        $this->s_repository =   $s_repository;
    }

    public function validationStore(array $data)
    {
        $ids = collect($data['lst_alerts'])->pluck('id')->toArray();
        $alerts = $this->s_repository->findAlertApps($ids);

        foreach ($alerts as $alert) {
            if ($alert->status !== 'PENDIENTE') {
                throw new Exception("Notificación inválida: {$alert->content}");
            }
        }
    }
}
