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
    public static function getCorrelative($type_sale_id): object
    {
        $correlative        =   null;

        //======= CONTABILIZANDO SI HAY DOCUMENTOS DE VENTA EMITIDOS PARA EL TYPE SALE ======
        $last_correlative   =   Sale::where('type_sale_id', $type_sale_id)->max('correlative');
        $serialization      =   DocumentSerialization::where('company_id', 1)->where('document_type_id', $type_sale_id)->first();

        if (!$serialization) {
            throw new Exception('No existe serialización configurada');
        }

        $correlative = $last_correlative
            ? $last_correlative + 1
            : $serialization->start_number;

        return (object)['correlative' => $correlative, 'serie' => $serialization->serie];
    }
}
