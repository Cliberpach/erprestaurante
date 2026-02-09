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
  "configuration_password_2" => "asdasdasd"
]
*/
    public function store(ConfigurationRequest $request)
    {
        DB::beginTransaction();
        try {

            $config_1   =   $request->get('configuration_1') === 'on' ? 'PRODUCTION' : 'BETA';
            $config_2   =   $request->get('configuration_2') === 'on' ? true : false;

            $config             =   Configuration::findOrFail(1);
            $config->property   =   $config_1;
            $config->save();

            if ($config_2) {
                $config             =   Configuration::findOrFail(2);
                $config->property   =   trim($request->get('configuration_password_2'));
                $config->status     =   $config_2;
                $config->save();
            }

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
            $config     =   Configuration::findOrFail(2);

            if ($config->status === 1 && $config->property !== $password) {
                throw new Exception("Contraseña incorrecta");
            }

            return response()->json(['success' => true, 'message' => 'Contraseña correcta']);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
