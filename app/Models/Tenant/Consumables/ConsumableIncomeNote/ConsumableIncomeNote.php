<?php

namespace App\Models\Tenant\Consumables\ConsumableIncomeNote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableIncomeNote extends Model
{
    use HasFactory;

    protected $table = 'consumable_income_notes';
    protected $connection   =   'tenant';
    protected $fillable = [
        'warehouse_id',
        'warehouse_name',
        'user_recorder_id',
        'user_recorder_name',
        'observation',
        'estado',
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
