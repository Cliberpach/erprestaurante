<?php

namespace App\Models;

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
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function proofPayment()
    {
        return $this->belongsTo(ProofPayment::class, 'proof_payment_id');
    }
}
