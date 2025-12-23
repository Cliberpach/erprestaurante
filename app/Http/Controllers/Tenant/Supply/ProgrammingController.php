<?php

namespace App\Http\Controllers\Tenant\Supply;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Supply\Programming\ProgrammingManager;
use App\Models\Tenant\Supply\Programming\Programming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Throwable;
use Yajra\DataTables\DataTables;

class ProgrammingController extends Controller
{
    private ProgrammingManager $s_manager;

    public function __construct()
    {
        $this->s_manager  =   new ProgrammingManager();
    }


    public function index()
    {
        return view('supply.programming.index');
    }

    public function getList(Request $request)
    {

        $items = Programming::from('programming as p')
            ->select(
                'p.id',
                'p.petty_cash_book_id',
                DB::raw('CONCAT("CM-", LPAD(p.petty_cash_book_id, 6, "0")) as petty_cash_book_code'),
                'p.petty_cash_name',
                'p.creator_user_name',
                'p.created_at',
                'p.updated_at'
            )
            ->where('p.status', 'ACTIVO');

        return DataTables::of($items)
            ->filterColumn('petty_cash_book_code', function ($query, $keyword) {
                $query->whereRaw("CONCAT('CM-', LPAD(p.petty_cash_book_id, 6, '0')) LIKE ?", ["%{$keyword}%"]);
            })
            ->toJson();
    }

    public function create()
    {
        $types_dish = UtilController::getTypesDish();
        return view('supply.programming.create', compact('types_dish'));
    }

/*
array:8 [ // app\Http\Controllers\Tenant\Supply\ProgrammingController.php:50
  "_token" => "qGlvuzy47KHzjWl81SNfxb9CikhiFe2wRB8qTZaz"
  "_method" => "POST"
  "cash_available_id" => "1"
  "producto" => null
  "purchase_price" => null
  "sale_price" => null
  "cantidad" => null
  "lst_detail" => "[{"product_id":2,"product_name":"SECO A LA NORTEÑA","type_dish_name":"SEGUNDO","purchase_price":"4.000000","sale_price":"17.000000","quantity":"200"}]"
]
*/
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $this->s_manager->store($request->all());

            DB::commit();

            Session::flash('message_success', 'Programación registrada con éxito.');
            return response()->json(['success' => true, 'message' => 'Programación registrada con éxito.']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage(), 'line' => $th->getLine(), 'file' => $th->getFile()]);
        }
    }
}
