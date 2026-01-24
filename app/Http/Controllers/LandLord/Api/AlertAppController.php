<?php

namespace App\Http\Controllers\LandLord\Api;

use App\Events\AlertAppEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Landlord\Api\AlertApp\AlertAppRequest;
use App\Models\Landlord\Api\AlertApp;
use App\Models\Tenant\Api\AlertApp as TenantAlertApp;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;
use Throwable;
use Illuminate\Support\Facades\Log;

class AlertAppController extends Controller
{
    public function store(AlertAppRequest $request)
    {
        $data = $request->validated();

        try {
            /**
             * =========================
             * 1️⃣ GUARDAR EN LANDLORD
             * =========================
             */
            $alert  =   DB::connection('landlord')->transaction(function () use ($data) {

                $item   =   AlertApp::create($data);

                Log::channel('alerts_app')->info('Alerta guardada en landlord', [
                    'tenant_domain' => $data['tenant_domain'],
                    'content'       => $data['content'],
                ]);

                return $item;
            });

            /**
             * =========================
             * 2️⃣ GUARDAR EN TENANT
             * =========================
             */
            $tenant = Tenant::where('domain', $data['tenant_domain'])->firstOrFail();
            $tenant->makeCurrent();

            DB::connection('tenant')->transaction(function () use ($data) {
                TenantAlertApp::create($data);
            });

            event(new AlertAppEvent($alert));

            return response()->json([
                'success' => true,
                'message' => 'NOTIFICACIÓN RECIBIDA'
            ], 200);
        } catch (Throwable $th) {

            Log::channel('alerts_app')->error('Error al procesar alerta', [
                'error_message' => $th->getMessage(),
                'file'          => $th->getFile(),
                'line'          => $th->getLine(),
                'trace'         => $th->getTraceAsString(),
                'payload'       => $data,
            ]);

            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        } finally {
            Tenant::forgetCurrent();
        }
    }
}
