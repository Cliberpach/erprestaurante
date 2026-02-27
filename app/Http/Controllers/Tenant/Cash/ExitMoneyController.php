<?php

namespace App\Http\Controllers\Tenant\Cash;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FormatController;
use App\Http\Requests\Tenant\Cash\ExitMoney\ExitMoneyStoreRequest;
use App\Http\Requests\Tenant\Cash\ExitMoney\ExitMoneyUpdateRequest;
use App\Http\Services\Tenant\Cash\ExitMoney\ExitMoneyManager;
use App\Models\Company;
use App\Models\ProofPayment;
use App\Models\Supplier;
use App\Models\Tenant\Cash\ExitMoney\ExitMoney;
use App\Models\Tenant\Cash\ExitMoney\ExitMoneyDetail;
use App\Models\Tenant\Maintenance\CostCenter;
use App\Models\Tenant\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ExitMoneyController extends Controller
{
    private ExitMoneyManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new ExitMoneyManager();
    }

    public function index(Request $request)
    {
        $exit_money = ExitMoney::where('status', true);
        $from_today = now()->format('Y-m-d');
        $to_today = now()->format('Y-m-d');

        if ($request->from_date && $request->to_date) {
            $exit_money = $exit_money->where('date', '>=', $request->from_date)->where('date', '<=', $request->to_date);
            $from_today = $request->from_date;
            $to_today = $request->to_date;
        }

        $exit_money = $exit_money->get();

        return view('cash.exit-money.index', compact('exit_money', 'from_today', 'to_today'));
    }

    public function getExitMoneys(Request $request)
    {
        $query = DB::connection('tenant')
            ->table('exit_money as em')
            ->join('suppliers as s', 's.id', '=', 'em.supplier_id')
            ->join('petty_cash_books as pcb', 'pcb.id', 'em.petty_cash_book_id')
            ->select(
                'em.id',
                'em.date',
                'em.cost_center_name',
                's.name as supplier_name',
                'em.number',
                'em.total',
                'em.discount_cash',
                DB::raw("CONCAT('CM-', LPAD(pcb.id, 8, '0')) as cash_book_code"),
            )
            ->where('em.status', 1);

        return DataTables::of($query)->toJson();
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $proof_payments = ProofPayment::all();
        $date = now()->format('Y-m-d');
        $payment_methods    =   PaymentMethod::where('estado', 'ACTIVO')->get();
        $cost_center        =   CostCenter::where('status', 'ACTIVO')->get();

        return view('cash.exit-money.create', compact(
            'suppliers',
            'proof_payments',
            'date',
            'payment_methods',
            'cost_center'
        ));
    }

    /*
array:8 [ // app\Http\Controllers\Tenant\Cash\ExitMoneyController.php:108
  "_token" => "plI5G8t5WW6JnSFBbtKhdipddMLzgqzr1XxElaSW"
  "proof_payment" => "1"
  "number" => "B002-11"
  "date" => "2026-02-27"
  "payment_method_id" => "1"
  "supplier_id" => "1"
  "cost_center" => "3"
  "lstDetails" => "[{"id":1772229118616,"description":"ALMUERZO","total":10}]"
]
*/
    public function store(ExitMoneyStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $exit   =   $this->s_manager->store($request->toArray());

            Session::flash('message_success', 'Egreso registrado con éxito');

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Egreso registrado con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function showPDF($id)
    {
        $exit_money = ExitMoney::findOrFail($id);
        $exit_money_detail = ExitMoneyDetail::where('exit_money_id', $exit_money->id)->get();
        $company = Company::first();


        $pdf = Pdf::loadView('cash.exit-money.reports.pdf-one', compact('exit_money', 'exit_money_detail', 'company'));

        return $pdf->stream('egreso_' . $exit_money->id . '.pdf');
    }

    public function editExit($id)
    {
        $proof_payments     =   ProofPayment::all();
        $payment_methods    =   PaymentMethod::where('estado', 'ACTIVO')->get();
        $cost_center        =   CostCenter::where('status', 'ACTIVO')->get();
        $exit_money         =   ExitMoney::findOrFail($id);
        $exit_money_detail  =   ExitMoneyDetail::where('exit_money_id', $exit_money->id)->get();
        $supplier_formatted =   FormatController::getFormatSupplier($exit_money->supplier_id);

        return view('cash.exit-money.edit', compact(
            'proof_payments',
            'payment_methods',
            'cost_center',
            'exit_money',
            'exit_money_detail',
            'supplier_formatted'
        ));
    }


    /*
array:9 [ // app\Http\Controllers\Tenant\Cash\ExitMoneyController.php:145
  "_token" => "plI5G8t5WW6JnSFBbtKhdipddMLzgqzr1XxElaSW"
  "proof_payment" => "1"
  "number" => "B002-12"
  "date" => null
  "payment_method_id" => "1"
  "supplier_id" => "1"
  "cost_center" => "3"
  "_method" => "PUT"
  "lstDetails" => "[{"id":1772230768407,"description":"A","total":10}]"
]
*/
    public function updateExit(ExitMoneyUpdateRequest $request, $id)
    {
        try {
            $exit_money =   $this->s_manager->update($request->toArray(), $id);
            Session::flash('message_success', 'Egreso actualizado con éxito');

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Egreso actualizado con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $exit_money =   $this->s_manager->destroy($id);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Egreso eliminado con éxito']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
