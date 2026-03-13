<?php

namespace App\Http\Controllers\Tenant\Consumable;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use Illuminate\Http\Request;

class ConsumableController extends Controller
{
    public function index()
    {
        $urlImagen = asset('assets/img/products/img_default.png');

        $categories =   UtilController::getConsumableCategories();
        $units      =   UtilController::getUnitsMeasurement();

        return view('tenant.consumables.consumable.index', compact(
            'urlImagen',
            'categories',
            'units'
        ));
    }
}
