<?php

namespace App\Models\Tenant\Alerts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertSale extends Model
{
    use HasFactory;
    protected $table = 'alerts_sales';
    protected $connection = 'tenant';

    protected $fillable = [
        'alert_id',
        'sale_id',
        'sale_serie',
        'matched_amount',
        'observation',
        'status',
        'creator_user_id',
        'creator_user_name',
        'editor_user_id',
        'editor_user_name',
        'deletor_user_id',
        'deletor_user_name',
    ];
}
