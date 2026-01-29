<?php

namespace App\Models\Tenant\Sales\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePay extends Model
{
    use HasFactory;

    protected $table        =   'sales_pays';
    protected $connection   =   'tenant';

    protected $fillable = [
        'payment_method_id',
        'payment_method_name',
        'sale_id',
        'amount'
    ];
}
