<?php

namespace App\Models\Landlord\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertApp extends Model
{
    use HasFactory;
    protected $table = 'alerts_app';
    protected $connection = 'landlord';

    protected $fillable = [
        'tenant_domain',
        'content',
        'sent_at',
    ];
}
