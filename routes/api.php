<?php

use App\Events\AlertAppEvent;
use App\Http\Controllers\LandLord\Api\AlertAppController;
use App\Http\Controllers\LandLord\ApiController;
use App\Models\Tenant\Api\AlertApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Multitenancy\Models\Tenant;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test-tenant', function () {
    return [
        'tenant' => Tenant::current(),
    ];
});

Route::post('/send-message', [AlertAppController::class, 'store']);

Route::get('/test-alert', function () {
    $alert =   AlertApp::first();

    event(new AlertAppEvent($alert));

    return response()->json([
        'message' => 'Evento enviado',
        'alert' => $alert
    ]);
});


Route::get('ListarPedidosPendientesImprimir', [ApiController::class, 'ListarPedidosPendientesImprimir']);
Route::get('ObtenerPedido_PorCodigo/{idpedido}', [ApiController::class, 'ObtenerPedido_PorCodigo']);
Route::get('ActualizarPedidosPendientesImprimir/{idpedido}', [ApiController::class, 'ActualizarPedidosPendientesImprimir']);
Route::get('ObtenerRecibo_PorCodigo/{idrecibo}', [ApiController::class, 'ObtenerRecibo_PorCodigo']);
Route::get('ActualizarReciboPendienteImprimir/{idrecibo}', [ApiController::class, 'ActualizarReciboPendienteImprimir']);
Route::get('ObtenerComanda_PorCodigo/{idpedido}', [ApiController::class, 'ObtenerComanda_PorCodigo']);
Route::get('ActualizarComandaPendienteImprimir/{idpedido}', [ApiController::class, 'ActualizarComandaPendienteImprimir']);
