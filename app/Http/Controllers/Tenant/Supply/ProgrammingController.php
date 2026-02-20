<?php

namespace App\Http\Controllers\Tenant\Supply;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Supply\Programming\ProgrammingManager;
use App\Models\Tenant\Supply\Programming\Programming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                DB::raw('CONCAT("CM-", LPAD(p.petty_cash_book_id,8, "0")) as petty_cash_book_code'),
                DB::raw('CONCAT("PR-", LPAD(p.id, 8, "0")) as programming_code'),
                'p.petty_cash_name',
                'p.creator_user_name',
                'p.created_at',
                'p.updated_at',
                'p.status'
            )
            ->where('p.status', '<>', 'ANULADO');

        return DataTables::of($items)
            ->filterColumn('petty_cash_book_code', function ($query, $keyword) {
                $query->whereRaw("CONCAT('CM-', LPAD(p.petty_cash_book_id, 6, '0')) LIKE ?", ["%{$keyword}%"]);
            })
            ->toJson();
    }

    public function create()
    {
        $types_dish =   UtilController::getTypesDish();
        $user       =   Auth::user();

        $roles      =   $user->getRoleNames();
        return view('supply.programming.create', compact(
            'types_dish',
            'roles',
            'user'
        ));
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

    public function edit($id)
    {
        try {
            $view   =   $this->s_manager->edit($id);

            return $view;
        } catch (Throwable $th) {
            Session::flash('message_success', $th->getMessage());
            return back();
        }
    }

    /*
    array:3 [ // app\Http\Controllers\Tenant\Supply\ProgrammingController.php:110
  "_token" => "QVx4SRF9HkvjrEFz2IdexYMnEKJCuicJNamjZESs"
  "_method" => "PUT"
  "lst_detail" => "[{"programming_id":3,"product_id":39,"product_name":"Crema Volteada","type_dish_name":"POSTRES","quantity":200,"purchase_price":"22.000000","sale_price":"38.000000"}]"
]
*/
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $programming    =   $this->s_manager->update($request->toArray(), $id);

            DB::commit();

            Session::flash('message_success', 'Programación actualizada con éxito.');
            return response()->json(['success' => true, 'message' => 'Programación actualizada con éxito.']);
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

    public function destroy(int $id)
    {
        DB::beginTransaction();
        try {

            $programming    =   $this->s_manager->destroy($id);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Programación eliminada con éxito.']);
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
}
