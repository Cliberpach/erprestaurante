<?php

namespace App\Http\Services\Tenant\Orders;

use App\Http\Controllers\FormatController;
use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Tenant\Reservation\Reservation;
use App\Models\Tenant\Supply\Programming\ProgrammingDetail;
use App\Models\Tenant\Supply\Table\Table;
use App\Models\Tenant\WarehouseProduct;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class OrderValidation
{
    private PettyCashBookService $s_cash_book;

    public function __construct()
    {
        $this->s_cash_book = new PettyCashBookService();
    }

    public function validationCreate(int $table_id): array
    {
        $table              =   Table::findOrFail($table_id);
        $categories         =   Category::all();
        $brands             =   Brand::all();
        $types_dish         =   UtilController::getTypesDish();
        $user               =   Auth::user();
        $petty_cash_book    =   $this->s_cash_book->getCashBookUser($user->id);
        $igv                =   round(Company::find(1)->igv, 2);
        $customer_formatted =   FormatController::getFormatInitialCustomer(1);

        if (!$petty_cash_book) {
            throw new Exception('DEBES PERTENECER A UNA CAJA ABIERTA!!!');
        }

        $programming    =   $this->s_cash_book->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception('SE DETECTÓ MÁS DE 1 PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }
        if ($programming === null) {
            throw new Exception('NO EXISTE NINGUNA PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }

        $vars   =   [
            'types_dish'        =>  $types_dish,
            'petty_cash_book'   =>  $petty_cash_book,
            'programming'       =>  $programming,
            'table'             =>  $table,
            'categories'        =>  $categories,
            'brands'            =>  $brands,
            'igv'               =>  $igv,
            'customer_formatted'    =>  $customer_formatted
        ];

        return $vars;
    }

    public function validationStore(array $data): array
    {
        $user               =   Auth::user();
        $petty_cash_book    =   $this->s_cash_book->getCashBookUser($user->id);
        if (!$petty_cash_book) {
            throw new Exception('DEBES PERTENECER A UNA CAJA ABIERTA!!!');
        }

        $programming    =   $this->s_cash_book->hasProgrammingActive($petty_cash_book->petty_cash_book_id);
        if ($programming === false) {
            throw new Exception('SE DETECTÓ MÁS DE 1 PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }
        if ($programming === null) {
            throw new Exception('NO EXISTE NINGUNA PROGRAMACIÓN ACTIVA EN LA CAJA!!!');
        }

        $table          =   Table::findOrFail($data['table_id']);
        $is_reservated  =   Reservation::where('table_id', $table->id)->where('status', 'OCUPADA')->first();

        if ($is_reservated) {
            throw new Exception("LA MESA: " . $table->name . " ESTÁ OCUPADA");
        }


        $lst_detail =   json_decode($data['lst_detail']);

        if (count($lst_detail) === 0) {
            throw new Exception("EL DETALLE DEL PEDIDO ESTÁ VACÍO!!!");
        }

        $data['table']      =   $table;
        $data['lst_detail'] =   $lst_detail;

        $this->validationLstDetail($lst_detail, $programming->id);

        return $data;
    }

    public function validationLstDetail(array $lst_detail, int $programming_id)
    {
        foreach ($lst_detail as $item) {

            if ($item->type_item === 'PLATO') {
                $item_bd = ProgrammingDetail::where('programming_id', $programming_id)->where('dish_id', $item->id)->first();
                if (!$item_bd) {
                    throw new Exception($item->name . ", NO EXISTE EN LA PROGRAMACIÓN");
                }
                if ($item_bd->stock < $item->quantity) {
                    throw new Exception("STOCK INSUFICIENTE: " . $item_bd->stock . " PARA LA CANT SOLICITADA: " . $item->quantity);
                }
            }

            if ($item->type_item === 'PRODUCTO') {
                $item_bd = WarehouseProduct::where('warehouse_id', 1)->where('product_id', $item->id)->first();
                if (!$item_bd) {
                    throw new Exception($item->name . ", NO EXISTE EN EL ALMACÉN");
                }
                if ($item_bd->stock < $item->quantity) {
                    throw new Exception("STOCK INSUFICIENTE: " . $item_bd->stock . " PARA LA CANT SOLICITADA: " . $item->quantity);
                }
            }
        }
    }
}
