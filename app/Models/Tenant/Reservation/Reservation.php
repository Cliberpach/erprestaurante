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
}
