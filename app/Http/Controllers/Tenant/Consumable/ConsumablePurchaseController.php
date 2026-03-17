<?php

namespace App\Http\Controllers\Tenant\Consumable;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Requests\Tenant\Consumables\Purchase\PurchaseStoreRequest;
use App\Http\Services\Tenant\Consumables\Purchase\PurchaseManager;
use App\Models\Supplier;
use App\Models\Tenant\Consumables\ConsumableBrand\ConsumableBrand;
use App\Models\Tenant\Consumables\ConsumableCategory\ConsumableCategory;
use App\Models\Tenant\Maintenance\Company\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ConsumablePurchaseController extends Controller
{
    private PurchaseManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new PurchaseManager();
    }

    public function index()
    {
        return view('tenant.consumables.purchases.index');
    }

    public function getAll(Request $request)
    {
        $items  =   $this->queryAll($request);

        return DataTables::of($items)->make(true);
    }

    public function queryAll(Request $request)
    {
        $supplier_id    =   $request->get('supplier');
        $status         =   $request->get('status');
        $start_date     =   $request->get('start_date');
        $end_date       =   $request->get('end_date');

        $items  =    DB::table('consumable_purchases as pd')
            ->select(
                'pd.id',
                'pd.delivery_date',
                'pd.supplier_name',
                'pd.supplier_type_document_abbreviation',
                'pd.supplier_document_number',
                'pd.currency',
                'pd.document_type',
                'pd.serie',
                'pd.correlative',
                'pd.observation',
                'pd.payment_status',
                'pd.payment_condition_name',
                'pd.cost_center_name'
            )
            ->where('pd.status', 'ACTIVO');

        if ($supplier_id) {
            $items->where('pd.supplier_id', $supplier_id);
        }
        if ($status) {
            $items->where('pd.payment_status', $status);
        }
        if ($start_date) {
            $items->whereDate('pd.created_at', '>=', $start_date);
        }
        if ($end_date) {
            $items->whereDate('pd.created_at', '<=', $end_date);
        }

        return $items;
    }

    public function create()
    {
        $categories                 =   ConsumableCategory::where('status', 'ACTIVO')->get();
        $brands                     =   ConsumableBrand::where('status', 'ACTIVO')->get();

        $suppliers                  =   Supplier::where('estado', 'ACTIVO')->get();

        $igv                        =   Company::findOrFail(1)->igv;

        $type_identity_documents    =   UtilController::getIdentityDocuments();
        $payment_conditions         =   UtilController::getPaymentConditions();
        $cost_center                =   UtilController::getCostCenter();

        return view(
            'tenant.consumables.purchases.create',
            compact(
                'categories',
                'brands',
                'suppliers',
                'igv',
                'type_identity_documents',
                'payment_conditions',
                'cost_center'
            )
        );
    }


    /*
array:16 [ // app\Http\Controllers\Tenant\Consumable\ConsumablePurchaseController.php:49
  "_token" => "90Mo4VF5pNlf6Ini9HnfOnWb2xPPPWaNSmZgXSdg"
  "payment_condition_id" => "1"
  "fecha_registro" => "2026-03-13"
  "expiration_date" => "2026-03-13"
  "fecha_entrega" => "2026-03-13"
  "tipo_doc" => "BOLETA"
  "proveedor" => "1"
  "cost_center" => "3"
  "serie" => "B003"
  "numero" => "122"
  "observation" => "test"
  "igv_chk" => "10.5000"
  "discount_cash" => "SI"
  "moneda" => "PEN"
  "lstPurchaseDocument" => "[{"product_id":19,"product_name":"SILLAO","category_name":"VERDURAS EDIT","brand_name":"SUPER MARCA","producto_unidad_medida":"NIU","quantity":"300","purchase_price":"1.00","almacen_id":null,"total":300}]"
  "igv_value" => "10.5000"
]
*/
    public function store(PurchaseStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $instance   =   $this->s_manager->store($request->toArray());

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Compra de insumos registrada con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json([
                'succes' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }

    public function show($id)
    {
        try {

            $data   =   $this->s_manager->show($id);

            return response()->json([
                'success' => true,
                'message' => 'DOCUMENTO COMPRA OBTENIDO',
                'data'  =>  $data
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }
}
