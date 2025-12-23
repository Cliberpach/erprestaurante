<?php

namespace App\Models\Tenant\Supply\Programming;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgrammingDetail extends Model
{
    use HasFactory;
    protected $table = 'programming_detail';

    protected $fillable = [
        'programming_id',
        'dish_id',

        'dish_name',
        'type_dish_name',

        'quantity',
        'purchase_price',
        'sale_price',

        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (auth()->check()) {
                $quote->creator_user_id = auth()->id();
                $quote->creator_user_name = auth()->user()->name;
            }
        });

        static::updating(function ($quote) {
            if (auth()->check()) {
                $quote->editor_user_id = auth()->id();
                $quote->editor_user_name = auth()->user()->name;
            }
            if ($quote->isDirty('status') && $quote->status === 'ANULADO') {
                if (auth()->check()) {
                    $quote->delete_user_id = auth()->id();
                    $quote->delete_user_name = auth()->user()->name;
                }
            }
        });
    }
}
