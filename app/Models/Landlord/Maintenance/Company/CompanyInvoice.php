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
    ];
}
