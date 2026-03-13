<?php

namespace App\Models\Tenant\Consumables\ConsumableKardex;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableKardex extends Model
{
    use HasFactory;

    protected $table    =   'consumable_kardex';
    protected $connection   =   'tenant';

    protected $fillable = [
        'purchase_id',
        'purchase_code',
        'note_income_id',
        'note_income_code',
        'note_release_id',
        'note_release_code',
        'type',
        'document_serie',
        'date',
        'warehouse_id',
        'warehouse_name',
        'consumable_id',
        'category_id',
        'brand_id',
        'consumable_unit',
        'consumable_name',
        'category_name',
        'brand_name',
        'quantity',
        'sale_price',
        'purchase_price',
        'amount',
        'creator_user_id',
        'creator_user_name',
        'create_date_master',
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
