<?php

namespace App\Models\Tenant\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDish extends Model
{
    use HasFactory;
    protected $table = 'orders_dishes';
    protected $connection = 'tenant';

    protected $fillable = [
        'order_id',
        'dish_id',
        'dish_name',
        'sale_price',
        'quantity',
        'status',
        'delete_status',
        'observation',
        'print_status',
        'print_delivery_status',
        'detail_printed',

        'purchase_price',
        'total',
        'type_dish_id',
        'type_dish_name',
        'created_at'
    ];
}
