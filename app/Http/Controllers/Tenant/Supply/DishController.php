<?php

namespace App\Http\Controllers\Tenant\Supply;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Requests\Tenant\Supply\Dish\DishStoreRequest;
use App\Http\Requests\Tenant\Supply\Dish\DishUpdateRequest;
use App\Http\Services\Tenant\Supply\Dish\DishManagement;
use App\Models\Landlord\ModelV;
use App\Models\Tenant\Supply\Dish\Dish;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Throwable;

class DishController extends Controller
{
    private DishManagement $s_manager;

    public function __construct()
    {
        $this->s_manager  =   new DishManagement();
    }

    public function index()
    {
        return view('supply.dishes.index');
    }

    public function getList(Request $request)
    {
        $type_dish_id = $request->get('type_dish_id');

        $items = Dish::from('dishes as d')
            ->join('types_dish as td', 'td.id', 'd.type_dish_id')
            ->select(
                'd.id',
                'd.name',
                'd.sale_price',
                'd.purchase_price',
                'd.img_route',
                'd.creator_user_name',
                'td.name as type_dish_name'
            )
            ->where('d.status', 'ACTIVO');

        if($type_dish_id){
            $items->where('d.type_dish_id', $type_dish_id);
        }

        return DataTables::of($items)->toJson();
    }

    public function create()
    {
        $types_dish =   UtilController::getTypesDish();
        return view('supply.dishes.create', compact('types_dish'));
    }

    public function getOne(int $id)
    {
        try {

            $item  =   $this->s_manager->getOne($id);

            return response()->json(['success' => true, 'message' => 'TIPO DE PLATO OBTENIDO', 'data' => $item]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:7 [ // app\Http\Controllers\Tenant\Supply\DishController.php:79
  "_token" => "DqXWf0GYW1K8Yug3TWzKqCQcnyVz6quC1fgxcYi9"
  "_method" => "POST"
  "type_dish_id" => "1"
  "name" => "ARROZ CON PATO"
  "purchase_price" => "1"
  "sale_price" => "12"
  "img" =>Illuminate\Http\UploadedFile {#2384
]
*/
    public function store(DishStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $item  =   $this->s_manager->store($request->toArray());

            DB::commit();
            return response()->json(['success' => true, 'message' => 'PLATO REGISTRADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }

    }

    public function edit(int $id)
    {
        $types_dish =   UtilController::getTypesDish();
        $dish       =   $this->s_manager->getOne($id);
        $img_route  =   $dish->img_route?asset($dish->img_route):null;

        return view('supply.dishes.edit', compact(
            'types_dish',
            'dish',
            'img_route'
        ));
    }

/*
array:7 [ // app\Http\Controllers\Tenant\Supply\DishController.php:128
  "_token" => "DqXWf0GYW1K8Yug3TWzKqCQcnyVz6quC1fgxcYi9"
  "_method" => "PUT"
  "type_dish_id" => "1"
  "name" => "ARROZ CON PATO"
  "purchase_price" => "1.00"
  "sale_price" => "14"
  "img" =>Illuminate\Http\UploadedFile {#2389
]
*/
    public function update(DishUpdateRequest $request, int $id)
    {
        DB::beginTransaction();
        try {
            $item  =   $this->s_manager->update($request->toArray(), $id);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'PLATO ACTUALIZADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {

            $item  =   $this->s_manager->destroy($id);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'PLATO ELIMINADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function searchModel(Request $request)
    {
        $term = $request->input('q');

        $results = ModelV::query()
            ->select('models.id', 'models.description as model', 'brandsv.description as brand')
            ->join('brandsv', 'brandsv.id', '=', 'models.brand_id')
            ->where('models.status', 'ACTIVE')
            ->where('brandsv.status', 'ACTIVE')
            ->when($term, function ($query, $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('models.description', 'like', "%{$term}%")
                        ->orWhere('brandsv.description', 'like', "%{$term}%");
                });
            })
            ->orderBy('brandsv.description')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => "{$item->brand} - {$item->model}"
                ];
            });

        return response()->json($results);
    }
}
