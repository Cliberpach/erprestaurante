<?php

namespace App\Http\Services\Tenant\Cash\PettyCashBook;

use App\Http\Controllers\UtilController;
use App\Models\Tenant\Accounts\CustomerAccountDetail;
use App\Models\Tenant\Cash\ExitMoney\ExitMoney;
use App\Models\Tenant\PaymentMethod;
use App\Models\Tenant\Sales\Sale\Sale;
use App\Models\Tenant\Supply\Dish\DishConsumable;
use Illuminate\Support\Facades\DB;

class PettyCashBookCalculator
{
    private PettyCashBookRepository $s_repository;

    public function __construct()
    {
        $this->s_repository =   new PettyCashBookRepository();
    }

    public function getConsolidated(int $id)
    {
        $haveModuleCustomerAccounts =   UtilController::haveModuleCustomerAccounts();

        $payment_methods            =   PaymentMethod::where('estado', 'ACTIVO')->get();

        $report_sales               =   $this->getReportSales($payment_methods, $id);
        $report_expenses            =   $this->getReportExpenses($payment_methods, $id);

        $report_customer_accounts   =   $this->getReportCustomerAccounts($payment_methods, $id);

        $petty_cash_book            =   $this->s_repository->getPettyCashBookInfo($id);
        $amount_close               =   $report_customer_accounts['total'] + $report_sales['total'] - $report_expenses['total'] + $petty_cash_book->initial_amount;

        $consolidated_consumables   =   $this->consolidatedConsumables($id);
        return [
            'report_sales'                  =>  $report_sales,
            'report_expenses'               =>  $report_expenses,
            'report_customer_accounts'      =>  $report_customer_accounts,
            'petty_cash_book'               =>  $petty_cash_book,
            'amount_close'                  =>  $amount_close,
            'have_module_customer_accounts' =>  $haveModuleCustomerAccounts,
            'consolidated_consumables'      =>  $consolidated_consumables
        ];
    }

    public function consolidatedCash($petty_cash_book, $consolidated)
    {
        $initial_amount     =   $petty_cash_book->initial_amount;
        $sales_amount       =   $consolidated['report_sales']['report'][0]['amount'];
        $expenses_amount    =   $consolidated['report_expenses']['report'][0]['amount'];
        $total              =   $initial_amount + $sales_amount - $expenses_amount;
        return (object)[
            'initial_amount'    =>  $initial_amount,
            'sales_amount'      =>  $sales_amount,
            'expenses_amount'   =>  $expenses_amount,
            'total'             =>  $total
        ];
    }

    public function getReportSales($payment_methods, int $id)
    {
        $_sales =   Sale::with(['pays', 'order.table'])->where('petty_cash_book_id', $id)->where('status', '<>', 'ANULADO')->get();
        $sales  =   $_sales->whereNull('converted_from_id');

        $change =   $sales->sum('change_pay');
        $pays   =   $sales->pluck('pays')->flatten();

        $report_sales   =   [];
        foreach ($payment_methods as $payment_method) {
            $amount = $pays
                ->where('payment_method_id', $payment_method->id)
                ->sum('total');

            if ($payment_method->id == 1) {
                $amount -=  $change;
            }

            $report_sales[] = [
                'payment_method_id'   => $payment_method->id,
                'payment_method_name' => $payment_method->description,
                'amount'              => $amount
            ];
        }

        $total  =   $sales->sum('total');

        return [
            'total' => $total,
            'report' => $report_sales,
            'sales' =>  $_sales
        ];
    }

    public function getReportExpenses($payment_methods, int $id)
    {
        $expenses    =   ExitMoney::where('petty_cash_book_id', $id)->where('status', true)->where('discount_cash', true)->get();

        $report_expenses = [];

        foreach ($payment_methods as $payment_method) {

            $amount = $expenses
                ->where('payment_method_id', $payment_method->id)
                ->sum('total');

            $report_expenses[] = [
                'payment_method_id'   => $payment_method->id,
                'payment_method_name' => $payment_method->description,
                'amount'              => $amount
            ];
        }

        $total = $expenses->sum('total');

        return [
            'total'         =>  $total,
            'report'        =>  $report_expenses,
            'exit_moneys'   =>  $expenses
        ];
    }

    public function getReportCustomerAccounts($payment_methods, int $id)
    {
        $customer_pays              =   CustomerAccountDetail::where('petty_cash_book_id', $id)->get();
        $report_customer_accounts   =   [];
        foreach ($payment_methods as $payment_method) {
            $item   =   [];
            $amount =   0;

            if ($payment_method->id === 1) {
                $amount   +=   $customer_pays->sum('cash');
            } else {
                $amount   =   $customer_pays->where('payment_method_id', $payment_method->id)->sum('amount');
            }


            $item       =   [
                'payment_method_id' =>  $payment_method->id,
                'payment_method_name' => $payment_method->description,
                'amount'            =>  $amount
            ];

            $report_customer_accounts[] =   $item;
        }

        $total  =   $customer_pays->sum('total');

        return ['total' => $total, 'report' => $report_customer_accounts];
    }

    public function consolidatedConsumables($id)
    {
        $report = DB::table('consumables as c')
            ->join('dish_consumables as dc', 'dc.consumable_id', '=', 'c.id')
            ->join('sales_dishes as sd', 'sd.dish_id', '=', 'dc.dish_id')
            ->join('sales as s', 's.id', '=', 'sd.sale_id')
            ->join('warehouse_consumables as wc', 'wc.consumable_id', 'c.id')
            ->select(
                'c.id',
                'c.name as consumable_name',
                'wc.stock',
                DB::raw('SUM(sd.quantity * dc.quantity) as total')
            )
            ->where('s.petty_cash_book_id', $id)
            ->groupBy('c.id', 'c.name', 'wc.stock')
            ->get();

        return $report;
    }
}
