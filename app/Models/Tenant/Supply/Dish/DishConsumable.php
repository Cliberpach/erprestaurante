<?php

namespace App\Models\Tenant\Supply\Dish;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DishConsumable extends Model
{
    use HasFactory;
    protected $table = 'dish_consumables';
    protected $connection   =   'tenant';

    protected $fillable = [
        'dish_id',
        'consumable_id',
        'quantity',
    ];
}
