<?php

namespace App\Http\Services\Landlord\Maintenance\Company;

use App\Models\Landlord\Company;
use App\Models\Landlord\Maintenance\Company\CompanyInvoice;
use App\Models\Module;
use App\Models\ModuleChild;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Tenant\Maintenance\Collaborator\Collaborator;
use App\Models\Tenant\Maintenance\Company\Company as TenantCompany;
use App\Models\Tenant\Maintenance\Company\CompanyInvoice as TenantCompanyInvoice;
use App\Models\Tenant\Maintenance\Company\DocumentSerialization;
use App\Models\Tenant\Maintenance\Company\Module as TenantModule;
use App\Models\Tenant\Maintenance\Company\ModuleChild as TenantModuleChild;
use App\Models\Tenant\Maintenance\Company\Plan as TenantPlan;
use App\Models\Tenant\User;
use Spatie\Permission\Models\Role;

class CompanyRepository
{
    public function storeTenant(array $dto): Tenant
    {
        return Tenant::create($dto);
    }

    public function storeCompanyLandlord(array $dto): Company
    {
        return Company::create($dto);
    }

    public function storeCompanyInvoiceLandlord(array $dto): CompanyInvoice
    {
        return CompanyInvoice::create($dto);
    }

    public function getModules(array $modules)
    {
        return Module::whereIn('id', $modules)->get();
    }
    public function getModulesChildren(array $childrens)
    {
        return ModuleChild::whereIn('id', $childrens)->get();
    }

    public function getPlan(int $id): Plan
    {
        return Plan::findOrFail($id);
    }

    public function storeCompanyTenant(array $dto): TenantCompany
    {
        return TenantCompany::create($dto);
    }

    public function storeCompanyInvoiceTenant(array $dto): TenantCompanyInvoice
    {
        return TenantCompanyInvoice::create($dto);
    }

    public function storeCollaboratorAdminTenant(array $dto): Collaborator
    {
        return Collaborator::create($dto);
    }

    public function storeUserAdminTenant(array $dto): User
    {
        return User::create($dto);
    }

    public function assignRoleAdmin(User $user)
    {
        $role = Role::where('name', 'admin')->first();
        $user->assignRole($role);
    }

    public function storeMasiveDocumentSerialiation(array $dto)
    {
        DocumentSerialization::insert($dto);
    }

    public function insertMasiveModulesTenant(array $dto)
    {
        TenantModule::insert($dto);
    }

    public function insertMasiveModulesChildrenTenant(array $dto)
    {
        TenantModuleChild::insert($dto);
    }

    public function storePlanTenant(array $dto): TenantPlan
    {
        return TenantPlan::create($dto);
    }

    public function saveLogoLandlord(Company $company, string $logo_url, string $logo_name)
    {
        $company->logo_url  =   $logo_url;
        $company->logo      =   $logo_name;
        $company->saveQuietly();
    }

    public function saveLogoTenant(TenantCompany $company, string $logo_url, string $logo_name)
    {
        $company->logo_url  =   $logo_url;
        $company->logo      =   $logo_name;
        $company->saveQuietly();
    }


    public function saveCertLandlord(CompanyInvoice $company_invoice, string $url, string $name)
    {
        $company_invoice->certificate       =   $name;
        $company_invoice->certificate_url   =   $url;
        $company_invoice->saveQuietly();
    }

    public function saveCertTenant(TenantCompanyInvoice $company_invoice, string $url, string $name)
    {
        $company_invoice->certificate           =   $name;
        $company_invoice->certificate_url       =   $url;
        $company_invoice->saveQuietly();
    }
}
