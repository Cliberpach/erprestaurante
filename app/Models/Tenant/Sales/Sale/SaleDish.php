<?php

namespace App\Models\Tenant\Sales\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDish extends Model
{
    use HasFactory;

    protected $table        =   'sales_dishes';
    protected $connection   =   'tenant';

    protected $fillable = [
        'sale_id',
        'dish_id',
        'dish_name',
        'type_dish_id',
        'type_dish_name',
        'programming_id',
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
        'created_at'
    ];
}
