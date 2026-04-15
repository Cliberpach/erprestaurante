<?php

namespace App\Http\Controllers\Tenant\Maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Configuration\ConfigurationRequest;
use App\Models\Tenant\Configuration;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConfigurationController extends Controller
{
    public function index()
    {
        $configuration  =   Configuration::all();
        return view('maintenance.configuration.index', compact('configuration'));
    }

/*
array:1 [ // app\Http\Controllers\Tenant\Maintenance\ConfigurationController.php:26
    "configuration_1" => "on"
    "configuration_2" => "on"
    "configuration_3" => "on"
    "configuration_4" => "123456789"
]
*/
    public function store(ConfigurationRequest $request)
    {
        DB::beginTransaction();
        try {

            $c1   =   $request->get('configuration_1') === 'on' ? 'PRODUCTION' : 'BETA';
            $c2   =   $request->get('configuration_2') === 'on' ? true : false;
            $c3   =   $request->get('configuration_3') === 'on' ? true : false;
            $c4   =   $request->get('configuration_4');

            $config             =   Configuration::findOrFail(1);
            $config->property   =   $c1;
            $config->save();


            $config2             =   Configuration::findOrFail(2);
            $config2->property   =   $c2;
            $config2->save();

            $config3             =   Configuration::findOrFail(3);
            $config3->property   =   $c3;
            $config3->save();

            $config4             =   Configuration::findOrFail(4);
            $config4->property   =   $c4;
            $config4->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'CONFIGURACIÓN GUARDADA']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function validationPassword(Request $request)
    {
        try {
            
            $password   =   trim($request->get('password'));
            $config     =   Configuration::findOrFail(4);

            if ($config->status === 1 && $config->property !== $password) {
                throw new Exception("Contraseña incorrecta");
            }

            return response()->json(['success' => true, 'message' => 'Contraseña correcta']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
