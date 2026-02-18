<?php

namespace App\Http\Services\Landlord\Maintenance\Company;

use App\Models\Tenant;

class CompanyManager
{

    protected CompanyService $s_company;

    public function __construct()
    {
        $this->s_company    =   new CompanyService();
    }

    public function store(array $data):Tenant
    {
        return $this->s_company->store($data);
    }
}
