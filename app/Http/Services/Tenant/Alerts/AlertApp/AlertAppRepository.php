<?php

namespace App\Http\Services\Tenant\Alerts\AlertApp;

use App\Models\Tenant\Api\AlertApp;
use Illuminate\Support\Facades\Auth;

class AlertAppRepository
{
    public function setStatus(array $lstAlerts)
    {
        $user   =   Auth::user();
        AlertApp::whereIn('id', collect($lstAlerts)->pluck('id')->toArray())
            ->update([
                'status' => 'USADO',
                'consumer_user_id' => $user->id,
                'consumer_user_name' => $user->name,
                'consumer_date' => now()
            ]);
    }
}
