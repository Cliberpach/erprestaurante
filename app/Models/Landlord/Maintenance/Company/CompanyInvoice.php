<?php

namespace App\Models\Landlord\Maintenance\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInvoice extends Model
{
    use HasFactory;

    protected $table = 'company_invoice';
    protected $connection   =   'landlord';
    protected $fillable = [
        'company_id',
        'certificate',
        'certificate_url',
        'certificate_password',
        'secondary_user',
        'secondary_password',
        'plan',
        'environment',
        'token_reniec',
        'status',
        'api_user_gre',
        'api_password_gre',

        'ubigeo',
        'department_id',
        'province_id',
        'district_id',
        'department_name',
        'province_name',
        'district_name'
    ];
}
