<?php

namespace App\Models\Tenant\Consumables\Consumable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumable extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'description',
        'sale_price',
        'purchase_price',
        'stock',
        'stock_min',
        'code_factory',
        'code_bar',
        'image',
        'img_route',
        'img_name',
        'unit_id',
        'unit_symbol',
        'unit_name',
        'status',
        'creator_user_id',
        'creator_user_name',
        'editor_user_id',
        'editor_user_name',
        'deletor_user_id',
        'deletor_user_name',
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

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->editor_user_id = auth()->id();
                $model->editor_user_name = auth()->user()->name;
            }
            if ($model->isDirty('status') && $model->status === 'ANULADO') {
                if (auth()->check()) {
                    $model->deletor_user_id = auth()->id();
                    $model->deletor_user_name = auth()->user()->name;
                }
            }
        });
    }
}
