<?php

namespace App\Http\Services\Landlord\Maintenance\Company;

use App\Models\Department;
use App\Models\District;
use App\Models\Landlord\Company;
use App\Models\Plan;
use App\Models\Province;
use App\Models\Tenant;
use App\Models\Tenant\Maintenance\Company\Company as TenantCompany;
use Illuminate\Support\Facades\Hash;

class CompanyDto
{
    public function getDtoTenant(array $data): array
    {
        $domain = strtolower($data['domain']);

        $domain = trim($domain);
        $domain = preg_replace('/\s+/', '', $domain);
        $domain = preg_replace('/[^a-z0-9\-\.]/', '', $domain);

        $dto = [
            'domain' => $domain . "." . parse_url(config("app.url"), PHP_URL_HOST),
            'name'   => $data['razon_social']
        ];

        return $dto;
    }

    public function getDtoCompanyLandlord(array $data, Tenant $tenant)
    {
        $tenantDirectory = $data['domain'] . '_' . $tenant->id;

        $dto    =   [
            'tenant_id'                 =>  $tenant->id,
            'ruc'                       =>  $data['ruc'],
            'business_name'             =>  $data['razon_social'],
            'abbreviated_business_name' =>  $data['razon_social_abreviada'],
            'fiscal_address'            =>  $data['direccion_fiscal'],
            'email'                     =>  $data['correo'],
            'plan'                      =>  $data['plan_id'],
            'files_route'               =>  $tenantDirectory,
            'token_placa'               =>  "nsHeEpNSOBr8ucEFnL7OtKmVkZhefUuvoM8O1Lz7uFEOi4KtFZ54==",
        ];

        return $dto;
    }

    public function getDtoCompanyInvoiceLandlord(array $data, Company $company)
    {
        $department =   Department::findOrFail($data['department']);
        $province   =   Province::findOrFail($data['province']);
        $district   =   District::findOrFail($data['district']);

        $dto    =   [
            'company_id'            =>  $company->id,
            'environment'           =>  'BETA',
            'token_reniec'          =>  'c36358c49922c564f035d4dc2ff3492fbcfd31ee561866960f75b79f7d645d7d',
            'plan'                  =>  'FULL',
            'secondary_user'        =>  $data['secondary_user'],
            'secondary_password'    =>  $data['secondary_password'],
            'api_user_gre'          =>  $data['api_user_gre'],
            'api_password_gre'      =>  $data['api_pass_gre'],
            'certificate_password'  =>  $data['certificate_password'],
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

    public function getDtoTenantCompany(array $data): array
    {
        $dto    =   [
            'ruc'                       =>  $data['ruc'],
            'business_name'             =>  $data['razon_social'],
            'abbreviated_business_name' =>  $data['razon_social_abreviada'],
            'domain'                    =>  $data['domain'],
            'files_route'               =>  $data['files_route'],
            'tenant_id'                 =>  $data['tenant_id'],
            'fiscal_address'            =>  $data['direccion_fiscal'],
            'email'                     =>  $data['correo'],
            'plan'                      =>  $data['plan_id'],
            'igv'                       =>  10.5
        ];
        return $dto;
    }

    public function getDtoTenantCompanyUpdate(array $data): array
    {
        $dto    =   [
            'ruc'                       =>  $data['ruc'],
            'business_name'             =>  $data['razon_social'],
            'abbreviated_business_name' =>  $data['razon_social_abreviada'],
            'fiscal_address'            =>  $data['direccion_fiscal'],
            'email'                     =>  $data['correo'],
            'plan'                      =>  $data['plan_id'],
        ];
        return $dto;
    }

    public function getDtoTenantCompanyInvoice(array $data, TenantCompany $company): array
    {
        $department =   Department::findOrFail($data['department']);
        $province   =   Province::findOrFail($data['province']);
        $district   =   District::findOrFail($data['district']);

        $dto =   [
            'company_id'           => $company->id,
            'plan'                 => $company->plan,
            'environment'          => 'BETA',
            'department_id'        => $department->id,
            'province_id'          => $province->id,
            'district_id'          => $district->id,
            'department_name'      => $department->name,
            'province_name'        => $province->name,
            'district_name'        => $district->name,
            'ubigeo'               => $district->id,
            'urbanization'         => 'PALERMO',
            'local_code'           => '0000',
            'certificate_password' => $data['certificate_password'],
            'secondary_user'       => $data['secondary_user'],
            'secondary_password'   => $data['secondary_password'],
            'api_user_gre'         => $data['api_user_gre'],
            'api_password_gre'     => $data['api_pass_gre'],
        ];
        return $dto;
    }

    public function getDtoCollaboratorTenant(): array
    {
        $dto =   [
            'full_name'             =>  'LUIS DANIEL ALVA LUJÁN',
            'document_type_id'      =>  1,
            'document_number'       =>  '77412431',
            'address'               =>  'AV HUSARES 123',
            'phone'                 =>  '999999999',
            'work_days'             =>  30,
            'rest_days'             =>  20,
            'monthly_salary'        =>  9999,
            'daily_salary'          =>  100,
            'position_id'           =>  1,
            'document_type_abbreviation'    =>  'DNI'
        ];
        return $dto;
    }

    public function getDtoUserTenant(array $data, $collaborator_id)
    {
        $dto    =   [
            'name'              =>  'ADMIN',
            'email'             =>  $data['correo'],
            'password'          =>  Hash::make($data['password']),
            'password_visible'  =>  $data['password'],
            'collaborator_id'   =>  $collaborator_id
        ];
        return $dto;
    }

    public function getDtoUpdateUserTenant(array $data)
    {
        $dto    =   [
            'email'             =>  $data['correo'],
            'password'          =>  Hash::make($data['password']),
            'password_visible'  =>  $data['password'],
        ];
        return $dto;
    }

    public function getDtoDocumentSerializationTenant(int $company_tenant_id): array
    {
        return [
            [
                'company_id'        => $company_tenant_id,
                'document_type_id'  => 67,
                'serie'             => 'TK01',
                'number_limit'      => 8,
                'destiny'           => 'TICKET',
                'default'           => 'NO',
                'final_number'      => 0,
                'description'       => 'TICKET',
                'created_at'        =>  now()
            ],
            [
                'company_id'        => $company_tenant_id,
                'document_type_id'  => 65,
                'serie'             => 'B001',
                'number_limit'      => 8,
                'destiny'           => 'BOLETA ELECTRÓNICA',
                'default'           => 'NO',
                'final_number'      => 0,
                'description'       => 'BOLETA ELECTRÓNICA',
                'created_at'        =>  now()
            ],
            [
                'company_id'        => $company_tenant_id,
                'document_type_id'  => 66,
                'serie'             => 'F001',
                'number_limit'      => 8,
                'destiny'           => 'FACTURA ELECTRÓNICA',
                'default'           => 'NO',
                'final_number'      => 0,
                'description'       => 'FACTURA ELECTRÓNICA',
                'created_at'        =>  now()
            ]
        ];
    }

    public function getDtoModulesTenant($modules): array
    {
        $dto = [];

        foreach ($modules as $module) {
            $dto[] = [
                'id'           => $module->id,
                'description'  => $module->description,
                'order'        => $module->order,
                'icon'         => $module->icon,
                'render_order' => $module->render_order,
                'created_at'    =>  now()
            ];
        }

        return $dto;
    }

    public function getDtoModulesChildrenTenant($childrens): array
    {
        $dto = [];

        foreach ($childrens as $child) {
            $dto[] = [
                'id'          => $child->id,
                'module_id'   => $child->module_id,
                'description' => $child->description,
                'route_name'  => $child->route_name,
                'order'       => $child->order,
                'created_at'    =>  now()
            ];
        }

        return $dto;
    }

    public function getDtoPlanTenant(int $plan_id): array
    {
        $plan = Plan::on('landlord')->findOrFail($plan_id);
        return
            [
                'id'            => $plan->id,
                'description'   => $plan->description,
                'number_fields' => $plan->number_fields,
                'price'         => $plan->price,
            ];
    }

    public function getDtoUpdateCompanyLandlord(array $data): array
    {
        $dto    =   [
            'ruc'                       =>  $data['ruc'],
            'business_name'             =>  $data['razon_social'],
            'abbreviated_business_name' =>  $data['razon_social_abreviada'],
            'fiscal_address'            =>  $data['direccion_fiscal'],
            'email'                     =>  $data['correo'],
            'plan'                      =>  $data['plan_id'],
        ];

        return $dto;
    }
}
