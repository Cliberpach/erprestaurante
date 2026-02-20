<?php

namespace App\Models\Tenant\Supply\Programming;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programming extends Model
{
    use HasFactory;
    protected $table = 'programming';
    protected $connection   =   'tenant';

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

        'closer_user_id',
        'closer_user_name'
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
                    $model->delete_user_id = auth()->id();
                    $model->delete_user_name = auth()->user()->name;
                }
            }

            if ($model->isDirty('status') && $model->status === 'CERRADO') {
                if (auth()->check()) {
                    $model->closer_user_id = auth()->id();
                    $model->closer_user_name = auth()->user()->name;
                }
            }
        });
    }
}
