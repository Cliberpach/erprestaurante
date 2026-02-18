<?php

namespace App\Models\Landlord;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $connection   = 'landlord';
    protected $table        = 'companies';

    protected $fillable = [

        'tenant_id',

        'ruc',
        'business_name',
        'abbreviated_business_name',

        'files_route',
        'logo_url',
        'logo',
        'base64_logo',

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
        'plan',

        'token_placa',

        'department_id',
        'province_id',
        'district_id',

        'department_name',
        'province_name',
        'district_name',
    ];
}
