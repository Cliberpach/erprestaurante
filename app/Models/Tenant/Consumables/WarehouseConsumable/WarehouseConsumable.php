<?php

namespace App\Models\Tenant\Consumables\WarehouseConsumable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseConsumable extends Model
{
    use HasFactory;

    protected $table = 'warehouse_consumables';
    protected $connection   =   'tenant';
    protected $fillable = [
        'warehouse_id',
        'consumable_id',
        'stock'
    ];
}
