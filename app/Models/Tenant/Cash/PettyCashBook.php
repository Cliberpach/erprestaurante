<?php

namespace App\Models\Tenant\Cash;

use App\Models\Shift;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashBook extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'petty_cash_books';
    protected $connection   =   'tenant';

    protected $fillable = [
        'petty_cash_id',
        'status',
        'shift_id',
        'user_id',
        'initial_amount',
        'closing_amount',
        'initial_date',
        'final_date',
        'sale_day',
        'petty_cash_name',
    ];

    public function pettyCash()
    {
        return $this->belongsTo(PettyCash::class, 'petty_cash_id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

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
