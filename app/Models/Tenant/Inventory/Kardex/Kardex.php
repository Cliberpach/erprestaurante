<?php

namespace App\Models\Tenant\Inventory\Kardex;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    use HasFactory;

    protected $table = 'kardex';

    protected $fillable = [
        'sale_id',
        'sale_code',

        'order_id',
        'order_code',

        'purchase_id',
        'purchase_code',

        'note_income_id',
        'note_income_code',

        'note_release_id',
        'note_release_code',

        'note_credit_id',
        'note_credit_code',

        // Movimiento
        'type',
        'document_serie',
        'date',

        // Almacén
        'warehouse_id',
        'warehouse_name',

        // Producto
        'product_id',
        'category_id',
        'brand_id',
        'product_code',
        'product_unit',
        'product_description',
        'product_name',
        'category_name',
        'brand_name',

        // Valores
        'quantity',
        'sale_price',
        'purchase_price',
        'amount',

        // Cliente
        'customer_id',
        'customer_name',
        'customer_type_document_abbreviation',
        'customer_document_number',

        // Totales
        'total',
        'subtotal',
        'igv',

        // Auditoría
        'creator_user_id',
        'creator_user_name',

        'created_at',
        'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->creator_user_id = auth()->id();
                $model->creator_user_name = auth()->user()->name;
            }
        });
    }
}
