<?php

namespace App\Http\Services\Tenant\Maintenance\Company;

use App\Http\Controllers\UtilController;
use App\Models\Landlord\Company as LandlordCompany;
use App\Models\Tenant\Maintenance\Company\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompanyService
{
    private CompanyDto $s_dto;
    private CompanyRepository $s_repository;

    public function __construct()
    {
        $this->s_dto    =   new CompanyDto();
        $this->s_repository =   new CompanyRepository();
    }

    public function startInvoicing(int $company_id, int $type_sale_id)
    {
        //====== ACTUALIZAR FACTURACIÓN A INICIADA PARA EL TYPE SALE RESPECTIVO ======
        DB::table('document_serializations')
            ->where('company_id', $company_id)
            ->where('document_type_id', $type_sale_id)
            ->where('initiated', 'NO')
            ->update([
                'initiated'     => 'SI',
                'updated_at'    => Carbon::now()
            ]);
    }

    public function startInvoicingSymbol(int $company_id, string $symbol)
    {
        //====== ACTUALIZAR FACTURACIÓN A INICIADA PARA EL TYPE SALE RESPECTIVO ======
        DB::table('document_serializations')
            ->where('company_id', $company_id)
            ->where('symbol', $symbol)
            ->where('initiated', 'NO')
            ->update([
                'initiated'     => 'SI',
                'updated_at'    => Carbon::now()
            ]);
    }

    public function update(array $data, int $id): Company
    {
        $dto    =   $this->s_dto->getDtoCompany($data);
        $instance   =   $this->s_repository->update($dto, $id);

        $dto_company_invoice_tenant =   $this->s_dto->getDtoCompanyInvoiceTenant($data, $id);
        $this->s_repository->updateCompanyInvoiceTenant($id, $dto_company_invoice_tenant);

        //========== UPDATE LANDLORD TRANSACTION =======
        DB::connection('landlord')->beginTransaction();
        try {
            $dto_company_landlord           =   $this->s_dto->getDtoCompanyLandlord($data);

            $company_landlord               =   $this->s_repository->updateCompanyLandlord($instance->tenant_id, $dto_company_landlord);
            $dto_company_invoice_landlord   =   $this->s_dto->getDtoCompanyInvoiceLandlord($data, $company_landlord);
            $company_invoice_landlord       =   $this->s_repository->updateCompanyInvoiceLandlord($company_landlord->id, $dto_company_invoice_landlord);

            DB::connection('landlord')->commit();
        } catch (Throwable $e) {
            DB::connection('landlord')->rollBack();
            throw $e;
        }

        //========== MANEJO DE LOGO ===========
        $this->updateLogo($data, $instance, $company_landlord, $instance);

        return $instance;
    }

    public function updateLogo(array $data, $tenant_data, LandlordCompany $company_landlord, Company $company_tenant)
    {
        $logo       =   $data['logo'] ?? null;
        $logo_url   =   null;
        $logo_name  =   null;
        if ($logo) {

            //========= ELIMINAR LOGO PREVIO ===========
            if ($company_tenant->logo_url) {
                UtilController::deleteFile($company_tenant->logo_url);
            }

            UtilController::saveFileFromLandlord($logo, 'logo_principal', $company_tenant->files_route . '/logo');

            $logo_name  =   'logo_principal.' . $logo->getClientOriginalExtension();
            $logo_url   =   'storage/' . $company_tenant->files_route . '/logo/' . $logo_name;
        } else {

            //========= ELIMINAR LOGO PREVIO ===========
            if ($company_tenant->logo_url) {
                UtilController::deleteFile($company_tenant->logo_url);
            }
        }

        $this->s_repository->saveLogoTenant($company_tenant, $logo_url, $logo_name);

        DB::connection('landlord')->beginTransaction();
        try {
            $this->s_repository->saveLogoLandlord($company_landlord, $logo_url, $logo_name);
            DB::connection('landlord')->commit();
        } catch (Throwable $e) {
            DB::connection('landlord')->rollBack();
            throw $e;
        }
    }
}
