<?php

namespace App\Models\Tenant\Consumables\ConsumableIncomeNote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableIncomeNoteDetail extends Model
{
    use HasFactory;
    protected $table = 'consumable_income_note_details';
    protected $connection   =   'tenant';

    protected $fillable = [
        'consumable_income_note_id',
        'consumable_id',
        'consumable_brand_id',
        'consumable_category_id',
        'warehouse_id',
        'warehouse_name',
        'consumable_name',
        'consumable_brand_name',
        'consumable_category_name',
        'quantity',
    ];
}
