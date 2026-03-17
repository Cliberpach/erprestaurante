<?php

namespace App\Http\Services\Tenant\Maintenance\Company;

use App\Models\Landlord\Company as LandlordCompany;
use App\Models\Landlord\Maintenance\Company\CompanyInvoice as LandlordCompanyInvoice;
use App\Models\Tenant\Maintenance\Company\Company;
use App\Models\Tenant\Maintenance\Company\CompanyInvoice;

class CompanyRepository
{
    public function update(array $dto, int $id): Company
    {
        $instance   =   Company::findOrFail($id);
        $instance->update($dto);
        return $instance;
    }

     public function updateCompanyInvoiceTenant(int $company_id, array $dto)
    {
        $company_invoice    =   CompanyInvoice::where('company_id', $company_id)->first();
        $company_invoice->update($dto);
        return $company_invoice;
    }

    public function updateCompanyLandlord(int $tenant_id, $dto): LandlordCompany
    {
        $company    =   LandlordCompany::where('tenant_id', $tenant_id)->where('status', '1')->first();
        $company->update($dto);
        return $company;
    }

    public function updateCompanyInvoiceLandlord(int $company_id, array $dto)
    {
        $company_invoice    =   LandlordCompanyInvoice::where('company_id', $company_id)->first();
        $company_invoice->update($dto);
        return $company_invoice;
    }


    public function saveLogoLandlord(LandlordCompany $company, ?string $logo_url, ?string $logo_name)
    {
        $company->logo_url  =   $logo_url;
        $company->logo      =   $logo_name;
        $company->saveQuietly();
    }

    public function saveLogoTenant(Company $company, ?string $logo_url, ?string $logo_name)
    {
        $company->logo_url  =   $logo_url;
        $company->logo      =   $logo_name;
        $company->saveQuietly();
    }
}
