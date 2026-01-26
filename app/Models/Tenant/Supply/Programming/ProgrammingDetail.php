<?php

namespace App\Models\Tenant\Supply\Programming;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgrammingDetail extends Model
{
    use HasFactory;
    protected $table = 'programming_detail';
    protected $connection   =   'tenant';

    protected $fillable = [
        'programming_id',
        'dish_id',

        'dish_name',
        'type_dish_name',

        'quantity',
        'stock',
        'purchase_price',
        'sale_price',

        'status',
    ];

}
