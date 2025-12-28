<?php

namespace App\Http\Services\Tenant\Orders;

use App\Http\Controllers\UtilController;
use App\Http\Services\Tenant\Cash\PettyCashBook\PettyCashBookService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Tenant\Supply\Table\Table;
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
            'igv'               =>  $igv
        ];

        return $vars;
    }
}
