<?php

namespace App\Http\Controllers\Tenant\Maintenance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Requests\Tenant\Maintenance\BankAccount\BankAccountStoreRequest;
use App\Http\Requests\Tenant\Maintenance\BankAccount\BankAccountUpdateRequest;
use App\Http\Services\Tenant\Maintenance\BankAccount\BankAccountManager;
use App\Models\Tenant\Maintenance\BankAccount\BankAccount;
use App\Models\Tenant\Sale\PaymentMethodAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class BankAccountController extends Controller
{
    private BankAccountManager $s_manager;

    public function __construct()
    {
        $this->s_manager    =   new BankAccountManager();
    }

    public function index()
    {
        $banks  =   UtilController::getBanks();
        return view('maintenance.bank_accounts.index', compact('banks'));
    }

    public function getBankAccounts(Request $request)
    {
        $cuentas   =   DB::table('bank_accounts as c')
            ->select(
                'c.id',
                'c.holder',         // titular
                'c.bank_id',        // banco_id
                'c.bank_name',      // banco_nombre
                'c.account_number', // nro_cuenta
                'c.cci',            // cci
                'c.phone',          // celular
                'c.currency',       // moneda,
                'c.qr_url'
            )
            ->where('status', '<>', 'ANULADO'); // estado

        return DataTables::of($cuentas)->make(true);
    }

    /*
array:7 [ // app\Http\Controllers\Tenant\Maintenance\BankAccountController.php:55
  "_token" => "tl053q5vHVqcyrnHwQtTbM7S039yQjOwcAkblfdS"
  "holder" => "ALVA LUJAN LUIS DANIEL"
  "currency" => "SOLES"
  "account_number" => "66233343"
  "cci" => "423423443"
  "phone" => "918817134"
  "bank_id" => "42"
  "qr" =>Illuminate\Http\UploadedFile {#2363
]
*/
    public function store(BankAccountStoreRequest $request)
    {
        DB::beginTransaction();
        try {

            $account    =   $this->s_manager->store($request->toArray());

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTA REGISTRADA CON ÉXITO']);
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
array:15 [ // app\Http\Controllers\Tenant\Maintenance\BankAccountController.php:96
  "_token" => "W2IrNvvxkcHseBAAVATFvG7aoL5CB3146mVQWkzi"
  "holder_edit" => "ALVA LUJAN LUIS DANIEL"
  "currency_edit" => "SOLES"
  "account_number_edit" => "57412312425"
  "cci_edit" => "960123123456789"
  "phone_edit" => "989796856"
  "bank_id_edit" => "45"
  "holder" => "ALVA LUJAN LUIS DANIEL"
  "currency" => "SOLES"
  "account_number" => "57412312425"
  "cci" => "960123123456789"
  "phone" => "989796856"
  "bank_id" => "45"
  "qr" => Illuminate\Http\UploadedFile {#2346
]
*/
    public function update(BankAccountUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $item   =   $this->s_manager->update($request->toArray(), $id);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTA BANCARIA ACTUALIZADA CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $bank_account                    =   BankAccount::findOrFail($id);
            $bank_account->status            =   'ANULADO';
            $bank_account->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CUENTA ELIMINADA']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getListBankAccounts(Request $request)
    {
        try {
            $payment_method_id  =   $request->get('payment_method_id');
            $bank_accounts  =   PaymentMethodAccount::from('payment_method_accounts as pma')
                ->join('bank_accounts as ba', 'ba.id', 'pma.bank_account_id')
                ->select(
                    'pma.bank_account_id as id',
                    DB::raw('CONCAT(ba.bank_abbreviation,":",ba.account_number,"-",ba.phone) as text')
                )
                ->where('payment_method_id', $payment_method_id)->get();

            return response()->json(['success' => true, 'message' => 'CUENTAS OBTENIDAS', 'bank_accounts' => $bank_accounts]);
        } catch (Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function getBackAccountPayment(int $payment_method)
    {
        try {
            $account_activated  =   PaymentMethodAccount::from('payment_method_accounts as pma')
                ->join('bank_accounts as ba', 'ba.id', 'pma.bank_account_id')
                ->select(
                    'ba.id',
                    'ba.holder',
                    'ba.bank_name',
                    'ba.account_number',
                    'ba.cci',
                    'ba.phone',
                    'ba.currency',
                    'ba.qr_url'
                )
                ->where('pma.is_active', true)
                ->where('payment_method_id', $payment_method)->first();

            if (!$account_activated) {
                throw new Exception("EL MÉTODO DE PAGO NO TIENE CUENTA PRINCIPAL ASIGNADA");
            }

            $account_activated->qr_url    =   $account_activated->qr_url ? asset('storage/' . $account_activated->qr_url) : null;

            return response()->json([
                'success' => true,
                'message' => 'CUENTA OBTENIDA',
                'bank_account' => $account_activated
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
        }
    }
}
