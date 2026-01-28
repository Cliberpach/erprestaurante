<?php

namespace App\Models\Tenant\Sales\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
    use HasFactory;

    protected $table        =   'sales_products';
    protected $connection   =   'tenant';

    protected $fillable = [
        'sale_id',

        'warehouse_id',
        'warehouse_name',

        'product_id',
        'product_name',

        'category_id',
        'category_name',

        'brand_id',
        'brand_name',

        'purchase_price',
        'sale_price',
        'quantity',
        'total',

        'mto_valor_unitario',
        'mto_valor_venta',
        'mto_base_igv',
        'porcentaje_igv',
        'igv',
        'tip_afe_igv',
        'total_impuestos',
        'mto_precio_unitario',

        'status',
        'observation',
    ];
}
