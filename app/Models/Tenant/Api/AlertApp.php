<?php

namespace App\Models\Tenant\Api;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertApp extends Model
{
    use HasFactory;
    protected $table = 'alerts_app';
    protected $connection = 'tenant';

    protected $fillable = [
        'tenant_domain',
        'content',
        'sent_at',
    ];
}
