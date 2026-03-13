<?php

namespace App\Http\Controllers\Tenant\Consumable;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Consumables\ConsumableBrand\ConsumableBrandStoreRequest;
use App\Http\Requests\Tenant\Consumables\ConsumableBrand\ConsumableBrandUpdateRequest;
use App\Http\Services\Tenant\Consumables\ConsumableBrand\ConsumableBrandManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ConsumableBrandController extends Controller
{
    private ConsumableBrandManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new ConsumableBrandManager();
    }

    public function index()
    {
        return view('tenant.consumables.brand.index');
    }

    public function getAll(Request $request)
    {
        $data   =   $this->queryAll($request);
        return DataTables::of($data)->make(true);
    }

    public function queryAll(Request $request)
    {
        $data   =   DB::table('consumable_brands as cb')
            ->select(
                'cb.*'
            )->where('cb.status', 'ACTIVO');
        return $data;
    }

    /*
array:2 [ // app\Http\Controllers\Tenant\Consumable\ConsumableCategoryController.php:45
  "_token" => "1VxJRowymdaLWPOfDGxaUW1EWTDRW0xezappxB2w"
  "name" => "assas"
]
*/
    public function store(ConsumableBrandStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $data       =   $request->validated();
            $instance   =   $this->s_manager->store($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Marca registrada con éxito', 'data' => $instance]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    /*
array:3 [ // app\Http\Controllers\Tenant\Consumable\ConsumableCategoryController.php:72
  "_token" => "1VxJRowymdaLWPOfDGxaUW1EWTDRW0xezappxB2w"
  "_method" => "PUT"
  "name" => "VERDURAS EDIT"
]
*/
    public function update($id, ConsumableBrandUpdateRequest $request)
    {
        DB::beginTransaction();
        try {

            $instance   =   $this->s_manager->update($request->toArray(), $id);

            DB::commit();
            return response()->json(['success' => true, 'data' => $instance, 'message' => 'Marca actualizada con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $instance   =   $this->s_manager->destroy($id);

            DB::commit();
            return response()->json(['success' => true, 'message' => "Marca eliminada con éxito"]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
