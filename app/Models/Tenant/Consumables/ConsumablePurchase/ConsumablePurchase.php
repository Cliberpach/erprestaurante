<?php

namespace App\Models\Tenant\Consumables\ConsumablePurchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumablePurchase extends Model
{
    use HasFactory;

    protected $table = 'consumable_purchases';
    protected $connection   =   'tenant';
    protected $fillable = [
        'delivery_date',
        'warehouse_id',
        'warehouse_name',
        'supplier_id',
        'supplier_name',
        'supplier_type_document_abbreviation',
        'supplier_document_number',
        'cost_center_id',
        'cost_center_name',
        'currency',
        'document_type',
        'serie',
        'correlative',
        'observation',
        'prices_with_igv',
        'igv',
        'subtotal',
        'amount_igv',
        'total',
        'discount_cash',
        'status',
        'payment_condition_id',
        'payment_condition_name',
        'payment_condition_days',
        'payment_status',
        'registration_date',
        'expiration_date',
        'creator_user_id',
        'editor_user_id',
        'deletor_user_id',
        'deletor_user_name',
        'editor_user_name',
        'creator_user_name',
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
