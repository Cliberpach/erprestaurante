<?php

namespace App\Http\Services\Tenant\Alerts\AlertSale;

use Illuminate\Support\Facades\Auth;

class AlertSaleDto
{
    public function getDtoStore(array $data): array
    {
        $user       =   Auth::user();
        $lstAlerts  =   $data['lst_alerts'];
        $sale       =   $data['sale'];
        $dto        =   [];
        foreach ($lstAlerts as $alert) {
            $item   =   [
                'sale_id'           =>  $sale->id,
                'alert_id'          =>  $alert->id,
                'sale_serie'        =>  $sale->serie . '-' . $sale->correlative,
                'matched_amount'    =>  $sale->total,
                'observation'       =>  '',
                'creator_user_id'   =>  $user->id,
                'creator_user_name' =>  $user->name,
                'created_at'        =>  now(),
                'updated_at'        =>  now()
            ];

            $dto[]  =   $item;
        }

        return $dto;
    }
}
