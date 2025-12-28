<?php

namespace App\Models\Tenant\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $table = 'orders_products';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'sale_price',
        'quantity',
        'status',
        'delete_status',
        'observation',
        'print_status',
        'print_delivery_status',
        'detail_printed',
    ];
}
