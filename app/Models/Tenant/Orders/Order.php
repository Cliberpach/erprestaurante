<?php

namespace App\Models\Tenant\Orders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';

    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_type_document_abbreviation',
        'customer_document_number',
        'date',
        'place',
        'observation',
        'n_attempts_dishes',
        'n_attempts_products',
        'status',
        'status_invoice',
        'pending_print',
        'pending_kitchen_print',
        'kitchen_print_mode',
        'waiter_delete_status',
        'waiter_delete_name',
        'waiter_delete_user_id',
        'waiter_delete_at',
        'waiter_delete_observation',
        'cashier_delete_status',
        'cashier_delete_name',
        'cashier_delete_user_id',
        'cashier_delete_at',
        'creator_user_id',
        'creator_user_name',
        'editor_user_id',
        'editor_user_name',
        'delete_user_id',
        'delete_user_name',
        'code',
        'subtotal',
        'total',
        'igv',
    ];
}
