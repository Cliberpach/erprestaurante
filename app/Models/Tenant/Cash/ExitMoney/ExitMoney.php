<?php

namespace App\Models\Tenant\Cash\ExitMoney;

use App\Models\ProofPayment;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitMoney extends Model
{
    use HasFactory;
    protected $table = 'exit_money';

    protected $fillable = [
        'proof_payment_id',
        'supplier_id',
        'user_id',
        'number',
        'date',
        'total',
        'status',
        'payment_method_id',
        'payment_method_name',
        'petty_cash_book_id',
        'cost_center_id',
        'cost_center_name',
        'discount_cash',
        'purchase_id',

        'creator_user_id',
        'editor_user_id',
        'deletor_user_id',

        'deletor_user_name',
        'editor_user_name',
        'creator_user_name',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function proofPayment()
    {
        return $this->belongsTo(ProofPayment::class, 'proof_payment_id');
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
            if ($model->isDirty('status') && $model->status == '0') {
                if (auth()->check()) {
                    $model->deletor_user_id = auth()->id();
                    $model->deletor_user_name = auth()->user()->name;
                }
            }
        });
    }
}
