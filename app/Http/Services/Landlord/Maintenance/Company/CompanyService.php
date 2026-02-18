<?php

namespace App\Http\Services\Landlord\Maintenance\Company;

use App\Http\Controllers\UtilController;
use App\Models\Landlord\Company;
use App\Models\Landlord\Maintenance\Company\CompanyInvoice;
use App\Models\Tenant;
use App\Services\TenantPermissionCloner;
use App\Models\Tenant\Maintenance\Company\Company as TenantCompany;
use App\Models\Tenant\Maintenance\Company\CompanyInvoice as TenantCompanyInvoice;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CompanyService
{
    private CompanyDto $s_dto;
    private CompanyRepository $s_repository;

    public function __construct()
    {
        $this->s_dto    =   new CompanyDto();
        $this->s_repository =   new CompanyRepository();
    }

    public function store(array $data): Tenant
    {
        $dto_tenant =   $this->s_dto->getDtoTenant($data);
        $tenant     =   $this->s_repository->storeTenant($dto_tenant);

        $dto_company        =   $this->s_dto->getDtoCompanyLandlord($data, $tenant);
        $company_landlord   =   $this->s_repository->storeCompanyLandlord($dto_company);

        $dto_company_invoice_landlord   =   $this->s_dto->getDtoCompanyInvoiceLandlord($data, $company_landlord);
        $company_invoice_landlord       =   $this->s_repository->storeCompanyInvoiceLandlord($dto_company_invoice_landlord);

        $data['files_route']        =   $company_landlord->files_route;
        $data['tenant_id']          =   $tenant->id;
        $data['zip_code']           =   $company_landlord->district_id;
        $data['modules']            =   $this->s_repository->getModules($data['module_id']);
        $data['modules_childrens']  =   $this->s_repository->getModulesChildren($data['child_id']);
        $data['plan_id']            =   $data['plan_id'];
        $data['files_route']        =   $company_landlord->files_route;
        DB::connection('landlord')->commit();

        $data_tenant                =   $this->insertDataTenant($tenant, $data);

        $this->makeTenantFilesSpace($company_landlord);
        Tenant::forgetCurrent();

        if (
            isset($data['logo'])
        ) {
            $this->saveLogo($data['logo'], $tenant, $company_landlord, $data_tenant->company_tenant);
        }

        if (
            isset($data['certificate'])
        ) {
            $this->saveCertificate($data, $tenant, $company_invoice_landlord, $data_tenant->company_invoice_tenant);
        }

        return $tenant;
    }

    public function insertDataTenant(Tenant $tenant, array $data)
    {
        $tenant->makeCurrent();

        $dto_tenant_company =   $this->s_dto->getDtoTenantCompany($data);
        $tenant_company     =   $this->s_repository->storeCompanyTenant($dto_tenant_company);

        $dto_tenant_company_invoice =   $this->s_dto->getDtoTenantCompanyInvoice($data, $tenant_company);
        $tenant_company_invoice     =   $this->s_repository->storeCompanyInvoiceTenant($dto_tenant_company_invoice);

        app(TenantPermissionCloner::class)->clone();

        $dto_collaborator_tenant    =   $this->s_dto->getDtoCollaboratorTenant();
        $collaborator_tenant        =   $this->s_repository->storeCollaboratorAdminTenant($dto_collaborator_tenant);

        $dto_user_tenant            =   $this->s_dto->getDtoUserTenant($data, $collaborator_tenant);
        $user_tenant                =   $this->s_repository->storeUserAdminTenant($dto_user_tenant);

        $this->s_repository->assignRoleAdmin($user_tenant);

        $dto_document_serialization =   $this->s_dto->getDtoDocumentSerializationTenant($tenant_company->id);
        $this->s_repository->storeMasiveDocumentSerialiation($dto_document_serialization);

        $dto_modules_tenant         =   $this->s_dto->getDtoModulesTenant($data['modules']);
        $this->s_repository->insertMasiveModulesTenant($dto_modules_tenant);

        $dto_childrens_tenant       =   $this->s_dto->getDtoModulesChildrenTenant($data['modules_childrens']);
        $this->s_repository->insertMasiveModulesChildrenTenant($dto_childrens_tenant);

        $dto_plan                   =   $this->s_dto->getDtoPlanTenant($data['plan_id']);
        $plan_tenant                =   $this->s_repository->storePlanTenant($dto_plan);

        return (object)[
            'company_tenant'            =>  $tenant_company,
            'company_invoice_tenant'    =>  $tenant_company_invoice
        ];
    }

    public function makeTenantFilesSpace(Company $company)
    {
        if (!Storage::disk('public')->exists($company->files_route)) {
            Storage::disk('public')->makeDirectory($company->files_route);
        }
    }

    public function saveLogo($logo, Tenant $tenant, Company $company_landlord, TenantCompany $company_tenant)
    {
        UtilController::saveFileFromLandlord($logo, 'logo_principal', $company_landlord->files_route . '/logo');

        $logo_name  =   'logo_principal.' . $logo->getClientOriginalExtension();
        $logo_url   =   'storage/' . $company_landlord->files_route . '/logo/' . $logo_name;
        $this->s_repository->saveLogoLandlord($company_landlord, $logo_url, $logo_name);

        $tenant->makeCurrent();
        $this->s_repository->saveLogoTenant($company_tenant, $logo_url, $logo_name);
        Tenant::forgetCurrent();
    }

    public function saveCertificate(array $data, Tenant $tenant, CompanyInvoice $company_invoice_land, TenantCompanyInvoice $company_invoice_tenant)
    {
        $files_route    =   $data['files_route'];
        $this->operationCert($data, $files_route);

        $cert_name  =   'cert_production.pem';
        $cert_url   =   'storage/' . $files_route . '/greenter/certs/' . $cert_name;
        $this->s_repository->saveCertLandlord($company_invoice_land, $cert_url, $cert_name);

        $tenant->makeCurrent();
        $this->s_repository->saveCertTenant($company_invoice_tenant, $cert_url, $cert_name);
        Tenant::forgetCurrent();
    }


    public function operationCert(array $data, string $files_route)
    {
        if (!isset($data['certificate'])) {
            return;
        }

        $certificateFile        =   $data['certificate'];
        $certificate_password   =   isset($data['certificate_password']) ?? null;
        $extension              =   strtolower($certificateFile->getClientOriginalExtension());
        if (!in_array($extension, ['pem', 'p12'])) {
            throw ValidationException::withMessages([
                'certificate' => [
                    'El certificado debe tener extensión .pem o .p12.'
                ]
            ]);
        }

        if ($extension === 'p12' && !$certificate_password) {
            throw ValidationException::withMessages([
                'certificate_password' => [
                    'La contraseña del certificado es obligatoria cuando se sube un archivo P12.'
                ]
            ]);
        }

        $pemFilename        = 'cert_production.pem';
        $directoryPath      = $files_route . '/greenter/certs/';
        $pemFullPath        = storage_path('app/public/' . $directoryPath . $pemFilename);

        if (!Storage::disk('public')->exists($directoryPath)) {
            Storage::disk('public')->makeDirectory($directoryPath);
        }

        // ===== CASO 1: SUBEN UN .PEM =====
        if ($extension === 'pem') {
            $certificateFile->storeAs(
                $directoryPath,
                $pemFilename,
                'public'
            );
        }

        // ===== CASO 2: SUBEN UN .P12 =====
        if ($extension === 'p12') {
            $this->saveCertP12ToPem($certificateFile, $certificate_password, $pemFullPath);
        }
    }

    public function saveCertP12ToPem($certificateFile, string $certificate_password, $pemFullPath)
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $opensslPath = $isWindows
            ? '"C:\Program Files\OpenSSL-Win64\bin\openssl.exe"'
            : 'openssl';

        //$opensslPath = '"C:\Program Files\OpenSSL-Win64\bin\openssl.exe"';
        /*if (!file_exists(str_replace('"', '', $opensslPath))) {
                        throw new \Exception('OpenSSL no encontrado en el servidor');
                    }*/

        exec($opensslPath . ' version 2>&1', $check, $checkCode);
        if ($checkCode !== 0) {
            throw new Exception('OpenSSL no está disponible en el servidor');
        }

        // 📁 Rutas temporales
        $tempP12Path    = storage_path('app/temp_' . uniqid() . '.p12');
        $privateKeyPath = storage_path('app/private_' . uniqid() . '.key');
        $certPath       = storage_path('app/cert_' . uniqid() . '.crt');

        // Guardar P12 temporal
        $certificateFile->move(dirname($tempP12Path), basename($tempP12Path));

        if (!file_exists($tempP12Path)) {
            throw new Exception('No se pudo guardar el archivo P12 temporal');
        }

        $p12Password = $certificate_password;

        // 1️⃣ Extraer CLAVE PRIVADA
        $cmdPrivateKey = $opensslPath . ' pkcs12 -legacy ' .
            '-in ' . escapeshellarg($tempP12Path) . ' ' .
            '-nocerts -nodes ' .
            '-out ' . escapeshellarg($privateKeyPath) . ' ' .
            '-password pass:' . escapeshellarg($p12Password) . ' 2>&1';

        exec($cmdPrivateKey, $outKey, $codeKey);

        if ($codeKey !== 0) {
            logger()->error('Error extrayendo clave privada', $outKey);
            throw new Exception('Error al extraer la clave privada del P12');
        }

        // 2️⃣ Extraer CERTIFICADO DEL CONTRIBUYENTE
        $cmdCert = $opensslPath . ' pkcs12 -legacy ' .
            '-in ' . escapeshellarg($tempP12Path) . ' ' .
            '-clcerts -nokeys ' .
            '-out ' . escapeshellarg($certPath) . ' ' .
            '-password pass:' . escapeshellarg($p12Password) . ' 2>&1';

        exec($cmdCert, $outCert, $codeCert);

        if ($codeCert !== 0) {
            logger()->error('Error extrayendo certificado', $outCert);
            throw new Exception('Error al extraer el certificado del P12');
        }

        // 3️⃣ UNIR EN UN SOLO PEM (FORMATO SUNAT CORRECTO)
        $cleanPrivateKey = $this->cleanPem(file_get_contents($privateKeyPath));
        $cleanCert       = $this->cleanPem(file_get_contents($certPath));

        file_put_contents(
            $pemFullPath,
            $cleanPrivateKey . $cleanCert
        );

        // 🧹 Limpiar temporales
        @unlink($tempP12Path);
        @unlink($privateKeyPath);
        @unlink($certPath);
    }

    public function cleanPem($content)
    {
        preg_match_all(
            '/-----BEGIN ([A-Z ]+)-----.*?-----END \1-----/s',
            $content,
            $matches
        );

        return implode(PHP_EOL, $matches[0]) . PHP_EOL;
    }
}
