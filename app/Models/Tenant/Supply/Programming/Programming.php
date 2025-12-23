<?php

namespace App\Models\Tenant\Supply\Programming;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programming extends Model
{
    use HasFactory;
    protected $table = 'programming';

    protected $fillable = [

        'petty_cash_book_id',
        'petty_cash_id',
        'petty_cash_name',
        'user_id',

        'quantity_dishes',
        'total',
        'status',

        'creator_user_id',
        'creator_user_name',

        'editor_user_id',
        'editor_user_name',

        'delete_user_id',
        'delete_user_name',
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
