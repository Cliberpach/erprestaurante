<?php

namespace App\Http\Services\Tenant\Maintenance\Company;

use App\Models\Department;
use App\Models\District;
use App\Models\Landlord\Company as LandlordCompany;
use App\Models\Province;

class CompanyDto
{
    public function getDtoCompany(array $data): array
    {
        return [
            'business_name'              => $data['business_name'],
            'abbreviated_business_name'  => $data['abbreviated_business_name'],
            'fiscal_address'             => $data['fiscal_address'] ?? null,
            'phone'                      => $data['phone'] ?? null,
            'cellphone'                  => $data['cellphone'] ?? null,
            'zip_code'                   => $data['zip_code'] ?? null,
            'email'                      => $data['email'] ?? null,
            'facebook'                   => $data['facebook'] ?? null,
            'instagram'                  => $data['instagram'] ?? null,
            'web'                        => $data['web'] ?? null,
            'igv'                        => $data['igv'],
        ];
    }

    public function getDtoCompanyInvoiceTenant(array $data, int $company_id): array
    {
        $department = Department::findOrFail($data['department']);
        $province   = Province::findOrFail($data['province']);
        $district   = District::findOrFail($data['district']);

        $dto    =   [
            'company_id'            =>  $company_id,
            'ubigeo'                =>  $district->id,
            'department_id'         =>  $department->id,
            'province_id'           =>  $province->id,
            'district_id'           =>  $district->id,
            'department_name'        =>  $department->name,
            'province_name'         =>  $province->name,
            'district_name'         =>  $district->name
        ];
        return $dto;
    }

    public function getDtoCompanyLandlord(array $data): array
    {
        $dto    =   [
            'ruc'                       =>  $data['ruc'],
            'business_name'             =>  $data['business_name'],
            'abbreviated_business_name' =>  $data['abbreviated_business_name'],
            'fiscal_address'            =>  $data['fiscal_address'],
            'email'                     =>  $data['email'],
            'facebook'                   => $data['facebook'] ?? null,
            'instagram'                  => $data['instagram'] ?? null,
            'web'                        => $data['web'] ?? null,
            'phone'                      => $data['phone'] ?? null,
            'cellphone'                  => $data['cellphone'] ?? null
        ];

        return $dto;
    }

    public function getDtoCompanyInvoiceLandlord(array $data, LandlordCompany $company)
    {
        $department =   Department::findOrFail($data['department']);
        $province   =   Province::findOrFail($data['province']);
        $district   =   District::findOrFail($data['district']);

        $dto    =   [
            'company_id'            =>  $company->id,
            'ubigeo'                =>  $district->id,
            'department_id'         =>  $department->id,
            'province_id'           =>  $province->id,
            'district_id'           =>  $district->id,
            'department_name'        =>  $department->name,
            'province_name'         =>  $province->name,
            'district_name'         =>  $district->name
        ];
        return $dto;
    }
}
