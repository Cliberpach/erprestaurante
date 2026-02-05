<?php

use App\Http\Controllers\LandLord\Api\AlertAppController;
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

Route::get('impresoras', function () {
    Route::get('ListarPedidosPendientesImprimir', 'ApiController@ListarPedidosPendientesImprimir');
    Route::get('ObtenerPedido_PorCodigo/{idpedido}', 'ApiController@ObtenerPedido_PorCodigo');
    Route::get('ActualizarPedidosPendientesImprimir/{idpedido}', 'ApiController@ActualizarPedidosPendientesImprimir');
    Route::get('ObtenerRecibo_PorCodigo/{idrecibo}', 'ApiController@ObtenerRecibo_PorCodigo');
    Route::get('ActualizarReciboPendienteImprimir/{idrecibo}', 'ApiController@ActualizarReciboPendienteImprimir');
    Route::get('ObtenerComanda_PorCodigo/{idpedido}', 'ApiController@ObtenerComanda_PorCodigo');
    Route::get('ActualizarComandaPendienteImprimir/{idpedido}', 'ApiController@ActualizarComandaPendienteImprimir');
});
