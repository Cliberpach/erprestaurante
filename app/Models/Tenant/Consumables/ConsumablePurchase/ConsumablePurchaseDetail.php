<?php

namespace App\Models\Tenant\Consumables\ConsumablePurchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumablePurchaseDetail extends Model
{
    use HasFactory;
    protected $table = 'consumable_purchase_details';
    protected $connection   =   'tenant';

    protected $fillable = [
        'purchase_id',
        'consumable_id',
        'category_id',
        'brand_id',
        'warehouse_id',
        'warehouse_name',
        'consumable_name',
        'category_name',
        'brand_name',
        'quantity',
        'purchase_price',
        'subtotal',
    ];
}
