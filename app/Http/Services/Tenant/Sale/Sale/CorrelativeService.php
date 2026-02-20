<?php

namespace App\Http\Services\Tenant\Sale\Sale;

use App\Models\Company;
use App\Models\Product;
use App\Models\Tenant\DocumentSerialization;
use App\Models\Tenant\Sales\Sale\Sale;
use Exception;
use Illuminate\Support\Facades\DB;

class CorrelativeService
{

    public function __construct() {}

/*
{#2181 // app\Http\Services\Tenant\Sale\Sale\SaleService.php:41
  +"correlative": "1"
  +"serie": "NV01"
}
*/
    public static function getCorrelative($type_sale_id):object
    {
        $correlative        =   null;
        $serie              =   null;

        //======= CONTABILIZANDO SI HAY DOCUMENTOS DE VENTA EMITIDOS PARA EL TYPE SALE ======
        $count_sales        =   Sale::where('type_sale_id',$type_sale_id)->count();

        $serialization      =   DocumentSerialization::where('company_id',1)->where('document_type_id',$type_sale_id)->first();

        //==== SI LA CANT ES 0 =====
        if ($count_sales === 0) {

            //====== INICIAR DESDE EL STARTING NUMBER =======
            $correlative        =   $serialization->start_number;
            $serie              =   $serialization->serie;
        } else {
            //======= EN CASO YA EXISTAN DOCUMENTOS DE VENTA DEL TYPE SALE ======
            $correlative        =   $count_sales->cant  +   1;
            $serie              =   $serialization->serie;
        }

        return (object)['correlative' => $correlative, 'serie' => $serie];
    }
}
