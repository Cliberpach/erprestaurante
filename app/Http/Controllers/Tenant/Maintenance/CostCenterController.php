<?php

namespace App\Http\Controllers\Tenant\Maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Maintenance\CostCenter\CostCenterStoreRequest;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Maintenance\CostCenter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CostCenterController extends Controller
{


    /*
array:3 [ // app\Http\Controllers\Tenant\Maintenance\CostCenterController.php:25
  "_token" => "pSN85MJ92iBVh25z4q0YUzc4mreSq9jpf4H5fFgn"
  "_method" => "POST"
  "name" => "asdasdasdasd"
]
*/
    public function storeCostCenter(CostCenterStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $data   =   $request->validated();
            $cost   =   CostCenter::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'CONFIGURACIÓN GUARDADA',
                'item' => $cost
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
