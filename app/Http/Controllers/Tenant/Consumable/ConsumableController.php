<?php

namespace App\Http\Controllers\Tenant\Consumable;

use App\Exports\Tenant\Consumables\Consumable\ConsumableExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Requests\Tenant\Consumables\Consumable\ConsumableStoreRequest;
use App\Http\Requests\Tenant\Consumables\Consumable\ConsumableUpdateRequest;
use App\Http\Services\Tenant\Consumables\Consumable\ConsumableManager;
use App\Models\Tenant\Consumables\Consumable\Consumable;
use App\Models\Tenant\Maintenance\Company\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ConsumableController extends Controller
{
    private ConsumableManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new ConsumableManager();
    }

    public function index()
    {
        $urlImagen = asset('assets/img/products/img_default.png');

        $brands     =   UtilController::getConsumableBrands();
        $categories =   UtilController::getConsumableCategories();
        $units      =   UtilController::getUnitsMeasurement();

        return view('tenant.consumables.consumable.index', compact(
            'urlImagen',
            'categories',
            'units',
            'brands'
        ));
    }

    public function getAll(Request $request)
    {
        $data   =   $this->queryAll($request);
        return DataTables::of($data)->make(true);
    }

    public function queryAll(Request $request)
    {
        $items   =   DB::table('consumables as p')
            ->join('consumable_categories as c', 'c.id', 'p.category_id')
            ->join('consumable_brands as b', 'b.id', 'p.brand_id')
            ->leftJoin('warehouse_consumables as wp', function ($join) {
                $join->on('wp.consumable_id', '=', 'p.id')
                    ->where('wp.warehouse_id', 1);
            })->select(
                'p.id',
                'p.name',
                'p.description',
                'p.brand_id',
                'p.category_id',
                'c.name as category_name',
                'b.name as brand_name',
                'p.sale_price',
                'p.purchase_price',
                'wp.warehouse_id',
                DB::raw('COALESCE(wp.stock, 0) as stock'),
                'p.stock_min',
                'p.code_factory',
                'p.code_bar',
                'p.img_route',
                'p.unit_id',
                'p.unit_symbol'
            )->where('p.status', 'ACTIVO');
        return $items;
    }

    public function getList(Request $request)
    {
        try {
            $items  =   $this->s_manager->getList($request->toArray());
            return DataTables::of($items)->make(true);
        } catch (Throwable $th) {
            dd($th->getMessage());
        }
    }

    /*
array:12 [ // app\Http\Controllers\Tenant\ProductController.php:74
  "_token" => "toQgu5tmflxhBWA5u0kr4ZpszFEo4UdPaFmcqoRO"
  "name" => "ASDASASDZXC"
  "description" => "ASDZXC"
  "sale_price" => "1"
  "purchase_price" => "1"
  "stock" => "0"
  "stock_min" => "0"
  "code_factory" => null
  "code_bar" => null
  "category_id" => "1"
  "brand_id" => "1"
   "unit_id" => "121"
  "image" =>Illuminate\Http\UploadedFile
*/
    public function store(ConsumableStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $data       =   $request->validated();
            $instance   =   $this->s_manager->store($data);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Insumo registrado con éxito', 'data' => $instance]);
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
array:12 [ // app\Http\Controllers\Tenant\Consumable\ConsumableController.php:120
  "_token" => "W1o2lusoIE1zaEbuH7BaYlKE08uQnMFqJ42rJuYy"
  "name" => "ALBERJITA edit"
  "description" => "test"
  "sale_price" => "1.00"
  "purchase_price" => "1.00"
  "stock_min" => "1"
  "code_factory" => null
  "code_bar" => null
  "category_id" => "2"
  "brand_id" => "1"
  "unit_id" => "126"
  "deleteImg" => "null"
]
*/
    public function update($id, ConsumableUpdateRequest $request)
    {
        DB::beginTransaction();
        try {
            $data       =   $request->validated();
            $instance   =   $this->s_manager->update($id, $data);

            DB::commit();
            return response()->json(['success' => true, 'data' => $instance, 'message' => 'INSUMO ACTUALIZADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function excel(Request $request)
    {
        $company        =   Company::findOrFail(1);
        $data           =   $this->queryAll($request)->get();

        return Excel::download(new ConsumableExport($data, $request, $company), 'insumos_' . Carbon::now() . '.xlsx');
    }

    public function pdf(Request $request)
    {
        $company        =   Company::find(1);
        $data           =   $this->queryAll($request)->get();

        $pdf = Pdf::loadview('tenant.consumables.consumable.reports.pdf', [
            'company'   => $company,
            'data'      => $data,
            'filters'   => $request,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('insumos_' . Carbon::now()->format('Y_m_d_H_i_s') . '.pdf');
    }

    public function searchConsumable(Request $request)
    {
        $query          =   trim($request->get('q', ''));
        $warehouse_id   =   $request->get('warehouse_id');

        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $products = Consumable::from('consumables as p')
            ->leftjoin('warehouse_consumables as wp', 'wp.consumable_id', 'p.id')
            ->join('consumable_categories as c', 'c.id', 'p.category_id')
            ->join('consumable_brands as b', 'b.id', 'p.brand_id')
            ->where('p.status', 'ACTIVO')
            ->where(function ($q) use ($query) {
                $q->where('p.name', 'LIKE', "%{$query}%")
                    ->orWhere('c.name', 'LIKE', "%{$query}%")
                    ->orWhere('b.name', 'LIKE', "%{$query}%");
            })
            ->where(function ($q) use ($warehouse_id) {
                $q->where('wp.warehouse_id', $warehouse_id)
                    ->orWhereNull('wp.warehouse_id');
            })
            ->limit(20)
            ->select(
                'wp.warehouse_id',
                'p.id',
                'b.name as brand_name',
                'p.name',
                'c.name as category_name',
                'p.sale_price',
                DB::raw('COALESCE(wp.stock, 0) as stock')
            )->get();

        $data = $products->map(fn($p) => [
            'id' => $p->id,
            'text' => "{$p->name} - ($p->stock)",
            'subtext' => "{$p->category_name}-{$p->brand_name}",
            'sale_price' =>  $p->sale_price,
            'name'  =>  $p->name,
            'category_name' =>  $p->category_name,
            'brand_name'    =>  $p->brand_name,
            'stock'         =>  $p->stock,
        ]);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
