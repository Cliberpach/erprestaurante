<?php

namespace App\Http\Controllers\Tenant\Supply;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Supply\Table\TableStoreRequest;
use App\Http\Requests\Tenant\Supply\Table\TableUpdateRequest;
use App\Http\Requests\Tenant\Supply\TypeDish\TypeDishStoreRequest;
use App\Http\Requests\Tenant\Supply\TypeDish\TypeDishUpdateRequest;
use App\Http\Services\Tenant\Supply\Table\TableManagement;
use App\Http\Services\Tenant\Supply\TypeDish\TypeDishManagement;
use App\Models\Landlord\ModelV;
use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\Supply\TypeDish\TypeDish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Throwable;

class TypeDishController extends Controller
{
    private TypeDishManagement $s_type_dish;

    public function __construct()
    {
        $this->s_type_dish  =   new TypeDishManagement();
    }

    public function index()
    {
        return view('supply.types_dish.index');
    }

    public function getList(Request $request)
    {

        $items = TypeDish::where('status', 'ACTIVO');

        return DataTables::of($items)->toJson();
    }

    public function getOne(int $id)
    {
        try {

            $item  =   $this->s_type_dish->getOne($id);

            return response()->json(['success' => true, 'message' => 'TIPO DE PLATO OBTENIDO', 'data' => $item]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    /*
array:4 [ // app\Http\Controllers\Tenant\WorkShop\ModelController.php:79
  "_token" => "whjxe4Khd8ttu83I4cdp4MshzLG5d1HbDuXliTWt"
  "_method" => "POST"
  "description" => "als23"
  "brand_id" => "1"
]
*/
    public function store(TypeDishStoreRequest $request)
    {
        DB::beginTransaction();

        try {

            $modelo  =   $this->s_type_dish->store($request->toArray());

            DB::commit();
            return response()->json(['success' => true, 'message' => 'MESA REGISTRADA CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }


        if ($request->has('fetch') && $request->input('fetch') == 'SI') {
            return response()->json(['message' => 'success',    'data' => $color]);
        }

        Session::flash('success', 'Color creado.');
        return redirect()->route('almacenes.colores.index')->with('guardar', 'success');
    }

    /*
array:4 [ // app\Http\Controllers\Tenant\WorkShop\ModelController.php:102
  "_token" => "whjxe4Khd8ttu83I4cdp4MshzLG5d1HbDuXliTWt"
  "description_edit" => "ALS23"
  "brand_id_edit" => "2"
  "_method" => "PUT"
]
*/
    public function update(TypeDishUpdateRequest $request, int $id)
    {
        DB::beginTransaction();
        try {

            $table  =   $this->s_type_dish->update($request->toArray(), $id);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'MESA ACTUALIZADA CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }


    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {

            $table  =   $this->s_type_dish->destroy($id);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'MESA ELIMINADA CON ÉXITO']);
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
