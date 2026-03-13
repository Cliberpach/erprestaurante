<?php

namespace App\Http\Controllers\Tenant\Consumable;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Consumables\ConsumableCategory\ConsumableCategoryStoreRequest;
use App\Http\Requests\Tenant\Consumables\ConsumableCategory\ConsumableCategoryUpdateRequest;
use App\Http\Services\Tenant\Consumables\ConsumableCategory\ConsumableCategoryManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ConsumableCategoryController extends Controller
{
    private ConsumableCategoryManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new ConsumableCategoryManager();
    }

    public function index()
    {
        return view('tenant.consumables.category.index');
    }

    public function getAll(Request $request)
    {
        $data   =   $this->queryAll($request);
        return DataTables::of($data)->make(true);
    }

    public function queryAll(Request $request)
    {
        $data   =   DB::table('consumable_categories as cc')
            ->select(
                'cc.*'
            )->where('cc.status', 'ACTIVO');
        return $data;
    }

    /*
array:2 [ // app\Http\Controllers\Tenant\Consumable\ConsumableCategoryController.php:45
  "_token" => "1VxJRowymdaLWPOfDGxaUW1EWTDRW0xezappxB2w"
  "name" => "assas"
]
*/
    public function store(ConsumableCategoryStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $data       =   $request->validated();
            $category   =   $this->s_manager->store($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Categoría registrada con éxito', 'category' => $category]);
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
    public function update($id, ConsumableCategoryUpdateRequest $request)
    {
        DB::beginTransaction();
        try {

            $category   =   $this->s_manager->update($request->toArray(), $id);

            DB::commit();
            return response()->json(['success' => true, 'data' => $category, 'message' => 'Categoría actualizada con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $category   =   $this->s_manager->destroy($id);

            DB::commit();
            return response()->json(['success' => true, 'message' => "Categoría eliminada con éxito"]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
