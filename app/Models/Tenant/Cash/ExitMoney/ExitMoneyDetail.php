<?php

namespace App\Models\Tenant\Cash\ExitMoney;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExitMoneyDetail extends Model
{
    use HasFactory;
    protected $table = 'exit_money_detail';

    protected $fillable = [
        'exit_money_id',
        'description',
        'total',
    ];
    public function exitMoney()
    {
        return $this->belongsTo(ExitMoney::class, 'exit_money_id');
    }
}
