<?php

namespace App\Http\Controllers\Tenant\Supply;

use App\Exports\Tenant\Supply\Dish\DishExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Requests\Tenant\Supply\Dish\DishStoreRequest;
use App\Http\Requests\Tenant\Supply\Dish\DishUpdateRequest;
use App\Http\Services\Tenant\Supply\Dish\DishManagement;
use App\Models\Landlord\ModelV;
use App\Models\Tenant\Maintenance\Company\Company;
use App\Models\Tenant\Orders\OrderDish;
use App\Models\Tenant\Supply\Dish\Dish;
use App\Models\Tenant\Supply\Dish\DishConsumable;
use App\Models\Tenant\Supply\Programming\ProgrammingDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $items  =   $this->queryAll($request);
        return DataTables::of($items)->toJson();
    }

    public function queryAll(Request $request)
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

        if ($type_dish_id) {
            $items->where('d.type_dish_id', $type_dish_id);
        }

        return $items;
    }

    public function getListProgramming(Request $request)
    {
        $programming_id = $request->get('programming_id');
        $type_dish_id = $request->get('type_dish_id');

        $items = ProgrammingDetail::from('programming_detail as pd')
            ->join('dishes as d', 'd.id', 'pd.dish_id')
            ->join('types_dish as td', 'td.id', 'd.type_dish_id')
            ->select(
                'pd.stock',
                'd.name',
                'td.name as type_dish_name',
                'd.sale_price',
                'd.purchase_price',
                'd.img_route',
                'd.creator_user_name',
                'd.id',
                'pd.programming_id'
            )
            ->where('pd.status', 'ACTIVO')
            ->where('pd.programming_id', $programming_id);

        if ($type_dish_id) {
            $items->where('d.type_dish_id', $type_dish_id);
        }

        return DataTables::of($items)->toJson();
    }

    public function create()
    {
        $types_dish =   UtilController::getTypesDish();
        $categories =   UtilController::getConsumableCategories();
        $brands     =   UtilController::getConsumableBrands();

        return view('supply.dishes.create', compact(
            'types_dish',
            'categories',
            'brands'
        ));
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
array:7 [ // app\Http\Controllers\Tenant\Supply\DishController.php:129
  "_token" => "8PJhxgGikBz66zjhwwUOi0UlFZ1ntoicDw3YoNiM"
  "_method" => "POST"
  "type_dish_id" => "2"
  "name" => "Arroz con chancho"
  "purchase_price" => "0"
  "sale_price" => "12"
   "img" =>Illuminate\Http\UploadedFile {#2389}
  "lstSheet" => "[{"id":22,"quantity":0,"name":"PAPA AMARILLA","unit_name":"KILOGRAMO"},{"id":23,"quantity":3,"name":"CARAMELOS A1","unit_name":"UNIDAD"}]"
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
        $img_route  =   $dish->img_route ? asset($dish->img_route) : null;
        $categories =   UtilController::getConsumableCategories();
        $brands     =   UtilController::getConsumableBrands();
        $lst_sheet  =   $this->s_manager->formatLstSheet($id);

        return view('supply.dishes.edit', compact(
            'types_dish',
            'dish',
            'img_route',
            'categories',
            'brands',
            'lst_sheet'
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
  "lstSheet" => "[{"id":22,"name":"PAPA AMARILLA","unit_name":"KILOGRAMO","quantity":"0.40"},{"id":21,"name":"VERDURA CHINA","unit_name":"GRAMO","quantity":"100.00"}]
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

    public function validatedDishStock(Request $request)
    {
        try {

            $programming_id = $request->get('programming_id');
            $dish_id        = $request->get('dish_id');
            $quantity       = (float) $request->get('quantity');
            $order_id       = $request->get('order_id');

            $item = ProgrammingDetail::where('programming_id', $programming_id)
                ->where('dish_id', $dish_id)
                ->select('stock')
                ->first();

            if (!$item) {
                throw new Exception("Plato no encontrado en programación.");
            }

            $stock_actual = (float) $item->stock;

            if (!$order_id) {

                if ($quantity > $stock_actual) {
                    throw new Exception("STOCK INSUFICIENTE (Stock: $stock_actual, Requiere: $quantity)");
                }

                return response()->json([
                    'success' => true,
                    'message' => 'VALIDACIÓN COMPLETADA'
                ]);
            }

            $item_bd = OrderDish::where('order_id', $order_id)
                ->where('dish_id', $dish_id)
                ->first();

            if (!$item_bd) {

                if ($quantity > $stock_actual) {
                    throw new Exception("STOCK INSUFICIENTE (Stock: $stock_actual, Requiere: $quantity)");
                }

                return response()->json([
                    'success' => true,
                    'message' => 'VALIDACIÓN COMPLETADA (Nuevo item)'
                ]);
            }

            $cantidad_anterior  = (float) $item_bd->quantity;
            $diferencia         = $quantity - $cantidad_anterior;

            if ($diferencia <= 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'VALIDACIÓN COMPLETADA (Cantidad reducida o igual)'
                ]);
            }

            if ($diferencia > $stock_actual) {
                throw new Exception("STOCK INSUFICIENTE (Stock: $stock_actual, Requiere adicional: $diferencia)");
            }

            return response()->json([
                'success' => true,
                'message' => 'VALIDACIÓN COMPLETADA (Actualización con diferencia)'
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    public function searchDish(Request $request)
    {
        $data   =   $this->s_manager->searchDish($request->toArray());

        return response()->json(['data' => $data]);
    }

    public function excel(Request $request)
    {
        $company        =   Company::findOrFail(1);
        $data           =   $this->queryAll($request)->get();

        return Excel::download(new DishExport($data, $request, $company), 'platos_' . Carbon::now() . '.xlsx');
    }

    public function pdf(Request $request)
    {
        $company        =   Company::find(1);
        $data           =   $this->queryAll($request)->get();

        $pdf = Pdf::loadview('supply.dishes.reports.pdf', [
            'company'   => $company,
            'data'      => $data,
            'filters'   => $request,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('platos_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }
}
