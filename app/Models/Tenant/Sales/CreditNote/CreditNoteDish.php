<?php

namespace App\Models\Tenant\Sales\CreditNote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNoteDish extends Model
{
    use HasFactory;

    protected $table        =   'credit_notes_dishes';
    protected $connection   =   'tenant';

    protected $fillable = [
        'credit_note_id',
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
