<?php

namespace App\Http\Services\Tenant\Alerts\AlertSale;

use App\Http\Services\Tenant\Alerts\AlertApp\AlertAppService;

class AlertSaleService
{
    private AlertSaleDto $s_dto;
    private AlertSaleRepository $s_repository;
    private AlertSaleValidation $s_validation;

    public function __construct()
    {
        $this->s_dto    =   new AlertSaleDto();
        $this->s_repository =   new AlertSaleRepository();
        $this->s_validation =   new AlertSaleValidation($this->s_repository);
    }

    public function store(array $data)
    {
        $this->s_validation->validationStore($data);
        $dto    =   $this->s_dto->getDtoStore($data);
        $this->s_repository->storeMasive($dto);

        $s_alert_app    =   new AlertAppService();
        $s_alert_app->setStatus($data['lst_alerts']);
    }
}
