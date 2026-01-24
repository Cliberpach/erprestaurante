<?php

namespace App\Events;

use App\Models\Landlord\Api\AlertApp;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class AlertAppEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public AlertApp $alert;
    public int $tenantId;

    public function __construct(AlertApp $alert)
    {
        $this->alert = $alert;

        $tenant = Tenant::where('domain', $alert->tenant_domain)->first();

        if (! $tenant) {
            Log::channel('alerts_app')->error('Tenant no encontrado', [
                'tenant_domain' => $alert->tenant_domain,
                'alert_id' => $alert->id,
            ]);
            return;
        }

        $this->tenantId = (int) $tenant->id;

        Log::channel('alerts_app')->info('📡 AlertAppEvent emitido', [
            'alert_id'      => $alert->id,
            'tenant_id'     => $this->tenantId,
            'tenant_domain' => $alert->tenant_domain,
        ]);
    }


    /**
     * Canal público por tenant
     */
    public function broadcastOn(): Channel
    {
        return new Channel('alerts.' . $this->tenantId);
    }

    /**
     * Nombre del evento en frontend
     */
    public function broadcastAs(): string
    {
        return 'alert.created';
    }

    /**
     * Payload basado en alerts_app
     */
    public function broadcastWith(): array
    {
        return [
            'id'            => $this->alert->id,
            'tenant_domain' => $this->alert->tenant_domain,
            'content'       => $this->alert->content,
            'sent_at'       => $this->alert->sent_at,
            'created_at'    => $this->alert->created_at,
        ];
    }
}
