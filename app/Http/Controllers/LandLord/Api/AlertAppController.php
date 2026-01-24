<?php

namespace App\Http\Controllers\LandLord\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\Api\AlertApp\AlertAppRequest;
use App\Models\Landlord\Api\AlertApp;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;
use Throwable;
use Illuminate\Support\Facades\Log;

class AlertAppController extends Controller
{
    public function store(AlertAppRequest $request)
    {
        DB::beginTransaction();
        try {

            $data   =   $request->validated();

            Log::channel('alerts_app')->info('Nueva alerta recibida', [
                'tenant_domain' => $data['tenant_domain'],
                'content'       => $data['content'],
                'date_received' => now(),
            ]);

            $alert  =   AlertApp::create($data);

            $tenant = Tenant::where('domain', $data['tenant_domain'])->firstOrFail();
            $tenant->makeCurrent();

            $alert_tenant   =   Tenant::create($data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'NOTIFICACIÓN RECIBIDA'
            ]);
        } catch (Throwable $th) {
            DB::rollBack();

            Log::channel('alerts_app')->error('Error al procesar alerta', [
                'error_message' => $th->getMessage(),
                'file'          => $th->getFile(),
                'line'          => $th->getLine(),
                'trace'         => $th->getTraceAsString(),
                'payload'       => $data ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error procesando la notificación'
            ], 500);
        }
    }
}
