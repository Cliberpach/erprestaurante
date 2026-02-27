<?php

namespace App\Models\Tenant\Maintenance\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = 'companies';
    protected $guarded = [''];
    protected $connection = 'tenant';

    protected $fillable = [
        'ruc',
        'business_name',
        'abbreviated_business_name',
        'domain',
        'files_route',
        'tenant_id',
        'logo_url',
        'logo',
        'base64_logo',
        'lat',
        'lng',
        'fiscal_address',
        'zip_code',
        'phone',
        'cellphone',
        'email',
        'facebook',
        'instagram',
        'web',
        'invoicing_status',
        'status',
        'igv',
        'block_account',
        'plan',
    ];
}
