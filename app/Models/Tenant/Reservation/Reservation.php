<?php

namespace App\Models\Tenant\Reservation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $table = 'reservations';

    protected $fillable = [
        'table_id',
        'order_id',
        'customer_id',
        'date',
        'status',
        'estado_delete',
        'creator_user_id',
        'creator_user_name',
        'editor_user_id',
        'editor_user_name',
        'delete_user_id',
        'delete_user_name',
        'code',
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

        static::created(function ($model) {
            $model->code = 'RE-' . str_pad($model->id, 8, '0', STR_PAD_LEFT);
            $model->saveQuietly();
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
        });
    }
}
