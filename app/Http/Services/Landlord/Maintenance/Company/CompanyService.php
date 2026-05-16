<?php

namespace App\Http\Services\Landlord\Maintenance\Company;

use App\Http\Controllers\UtilController;
use App\Models\Landlord\Company;
use App\Models\Landlord\Maintenance\Company\CompanyInvoice;
use App\Models\Tenant;
use App\Services\TenantPermissionCloner;
use App\Models\Tenant\Maintenance\Company\Company as TenantCompany;
use App\Models\Tenant\Maintenance\Company\CompanyInvoice as TenantCompanyInvoice;
use App\Services\TestService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant\Maintenance\Collaborator\Collaborator;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CompanyService
{
    private CompanyDto $s_dto;
    private CompanyRepository $s_repository;
    private CompanyValidation $s_validation;

    public function __construct()
    {
        $this->s_dto    =   new CompanyDto();
        $this->s_repository =   new CompanyRepository();
        $this->s_validation =   new CompanyValidation();
    }

    /*public function store(array $data): Tenant
    {
        $this->s_validation->validationStore($data);

        $dto_tenant =   $this->s_dto->getDtoTenant($data);
        $tenant     =   $this->s_repository->storeTenant($dto_tenant);

        $dto_company        =   $this->s_dto->getDtoCompanyLandlord($data, $tenant);
        $company_landlord   =   $this->s_repository->storeCompanyLandlord($dto_company);

        $dto_company_invoice_landlord   =   $this->s_dto->getDtoCompanyInvoiceLandlord($data, $company_landlord);
        $company_invoice_landlord       =   $this->s_repository->storeCompanyInvoiceLandlord($dto_company_invoice_landlord);

        $data['files_route']        =   $company_landlord->files_route;
        $data['tenant_id']          =   $tenant->id;
        $data['zip_code']           =   $company_landlord->district_id;
        $data['modules']            =   $this->s_repository->getModulesLandlord($data['module_id']);
        $data['modules_childrens']  =   $this->s_repository->getModulesChildrenLandlord($data['child_id']);
        $data['plan_id']            =   $data['plan_id'];
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
    }*/

    public function store(array $data): Tenant
    {
        $this->s_validation->validationStore($data);

        $dto_tenant =   $this->s_dto->getDtoTenant($data);
        $tenant     =   $this->s_repository->storeTenant($dto_tenant);

        $dto_company        =   $this->s_dto->getDtoCompanyLandlord($data, $tenant);
        $company_landlord   =   $this->s_repository->storeCompanyLandlord($dto_company);

        $dto_company_invoice_landlord   =   $this->s_dto->getDtoCompanyInvoiceLandlord($data, $company_landlord);
        $company_invoice_landlord       =   $this->s_repository->storeCompanyInvoiceLandlord($dto_company_invoice_landlord);

        $data['files_route']        =   $company_landlord->files_route;
        $data['tenant_id']          =   $tenant->id;
        $data['zip_code']           =   $company_landlord->district_id;
        $data['modules']            =   $this->s_repository->getModulesLandlord($data['module_id']);
        $data['modules_childrens']  =   $this->s_repository->getModulesChildrenLandlord($data['child_id']);
        $data['plan_id']            =   $data['plan_id'];
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
        $tenant_company     =   $this->s_repository->updateCompanyTenant($dto_tenant_company,1);

        $dto_tenant_company_invoice =   $this->s_dto->getDtoTenantCompanyInvoice($data, $tenant_company);
        $tenant_company_invoice     =   $this->s_repository->updateCompanyInvoiceTenant($dto_tenant_company_invoice);

        // app(TenantPermissionCloner::class)->clone();

        // $dto_collaborator_tenant    =   $this->s_dto->getDtoCollaboratorTenant();
        $collaborator_tenant        =   $this->s_repository->getCollaboratorAdminTenant();

        $dto_user_tenant            =   $this->s_dto->getDtoUserTenant($data, $collaborator_tenant->id);
        $user_tenant                =   $this->s_repository->updateUserAdminTenant($dto_user_tenant);

        // $this->s_repository->assignRoleAdmin($user_tenant);

        // $dto_document_serialization =   $this->s_dto->getDtoDocumentSerializationTenant($tenant_company->id);
        // $this->s_repository->storeMasiveDocumentSerialiation($dto_document_serialization);

        // $dto_modules_tenant         =   $this->s_dto->getDtoModulesTenant($data['modules']);
        // $this->s_repository->insertMasiveModulesTenant($dto_modules_tenant);

        // $dto_childrens_tenant       =   $this->s_dto->getDtoModulesChildrenTenant($data['modules_childrens']);
        // $this->s_repository->insertMasiveModulesChildrenTenant($dto_childrens_tenant);

        // $dto_plan                   =   $this->s_dto->getDtoPlanTenant($data['plan_id']);
        // $plan_tenant                =   $this->s_repository->storePlanTenant($dto_plan);

        // $this->dataTestTenant();

        return (object)[
            'company_tenant'            =>  $tenant_company,
            'company_invoice_tenant'    =>  $tenant_company_invoice
        ];
    }


    /*public function insertDataTenantOld(Tenant $tenant, array $data)
    {
        $tenant->makeCurrent();

        $dto_tenant_company =   $this->s_dto->getDtoTenantCompany($data);
        $tenant_company     =   $this->s_repository->storeCompanyTenant($dto_tenant_company);

        $dto_tenant_company_invoice =   $this->s_dto->getDtoTenantCompanyInvoice($data, $tenant_company);
        $tenant_company_invoice     =   $this->s_repository->storeCompanyInvoiceTenant($dto_tenant_company_invoice);

        app(TenantPermissionCloner::class)->clone();

        $dto_collaborator_tenant    =   $this->s_dto->getDtoCollaboratorTenant();
        $collaborator_tenant        =   $this->s_repository->storeCollaboratorAdminTenant($dto_collaborator_tenant);

        $dto_user_tenant            =   $this->s_dto->getDtoUserTenant($data, $collaborator_tenant->id);
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

        $this->dataTestTenant();

        return (object)[
            'company_tenant'            =>  $tenant_company,
            'company_invoice_tenant'    =>  $tenant_company_invoice
        ];
    }*/

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
        $certificate_password   =   $data['certificate_password'] ?? null;
        $extension              =   strtolower($certificateFile->getClientOriginalExtension());

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

    public function saveCertP12ToPem($certificateFile, ?string $certificate_password, $pemFullPath)
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $opensslPath = $isWindows
            ? '"C:\Program Files\OpenSSL-Win64\bin\openssl.exe"'
            : 'openssl';

        $legacy =   $isWindows ? '' : '-legacy ';

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
        $cmdPrivateKey = $opensslPath . ' pkcs12 ' . $legacy .
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
        $cmdCert = $opensslPath . ' pkcs12 ' . $legacy .
            '-in ' . escapeshellarg($tempP12Path) . ' ' .
            '-clcerts -nokeys ' .
            '-out ' . escapeshellarg($certPath) . ' ' .
            '-password pass:' . $p12Password . ' 2>&1';

        exec($cmdCert, $outCert, $codeCert);

        if ($codeCert !== 0) {
            dd($outCert);
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

    public function edit(int $id)
    {
        $all_modules    =   $this->s_repository->getAllModules();
        $tenant_data    =   $this->s_repository->getTenantCompanyData($id);
        $tenant_modules =   $this->s_repository->getTenantModules($tenant_data->database);
        $tenant_modules_children    =   $this->s_repository->getTenantModulesChildren($tenant_data->database);
        $tenant_modules_grand_children    =   $this->s_repository->getTenantModulesGrandChildren($tenant_data->database);

        $user           =   $this->s_repository->getTenantUser($tenant_data->database);
        $plans          =   $this->s_repository->getPlans();
        $departments    =   UtilController::getDepartments();
        $provinces      =   UtilController::getProvinces();
        $districts      =   UtilController::getDistricts();

        return view('company.edit_company_landlord', compact(
            'all_modules',
            'plans',
            'tenant_data',
            'tenant_modules',
            'tenant_modules_children',
            'tenant_modules_grand_children',
            'user',
            'departments',
            'provinces',
            'districts'
        ));
    }

    public function update(array $data, int $id)
    {
        $this->s_validation->validationUpdate($data);

        //========= OBTENER DATA =======
        $tenant_data                =   $this->s_repository->getTenantCompanyData($id);
        $tenant                     =   $this->s_repository->findTenant($tenant_data->tenant_id);
        $data['modules']            =   $this->s_repository->getModulesLandlord($data['module_id'] ?? []);
        $data['modules_childrens']  =   $this->s_repository->getModulesChildrenLandlord($data['child_id'] ?? []);
        $data['files_route']        =   $tenant_data->files_route;

        //========== UPDATE LANDLORD TRANSACTION =======
        DB::connection('landlord')->beginTransaction();
        try {
            $dto_company_landlord   =   $this->s_dto->getDtoUpdateCompanyLandlord($data);
            $company_landlord       =   $this->s_repository->updateCompanyLandlord($id, $dto_company_landlord);

            $dto_company_invoice_landlord   =   $this->s_dto->getDtoCompanyInvoiceLandlord($data, $company_landlord);
            $company_invoice_landlord       =   $this->s_repository->updateCompanyInvoiceLandlord($tenant_data->company_invoice_id, $dto_company_invoice_landlord);

            DB::connection('landlord')->commit();
        } catch (Throwable $e) {
            DB::connection('landlord')->rollBack();
            throw $e;
        }

        //========= UPDATE TENANT TRANSACTION =========
        $res =   $this->updateDataTenant($tenant_data, $tenant, $data);

        //========== MANEJO DE LOGO ===========
        $this->updateLogo($data, $tenant_data, $company_landlord, $tenant, $res->company_tenant);

        //========= MANEJO CERTIFICADO ==========
        $this->updateCertificate($tenant_data, $data, $tenant, $company_invoice_landlord, $res->company_invoice_tenant);
    }

    public function updateDataTenant($tenant_data, Tenant $tenant, array $data)
    {
        $tenant->makeCurrent();

        DB::connection('tenant')->beginTransaction();
        try {
            //=========== MODULOS =============
            $this->s_repository->deleteTenantModulesGrandChildren($tenant_data->database);
            $this->s_repository->deleteTenantModulesChildren($tenant_data->database);
            $this->s_repository->deleteTenantModules($tenant_data->database);


            $dto_modules_tenant         =   $this->s_dto->getDtoModulesTenant($data['modules']);
            $this->s_repository->insertMasiveModulesTenant($dto_modules_tenant);

            $dto_childrens_tenant       =   $this->s_dto->getDtoModulesChildrenTenant($data['modules_childrens']);
            $this->s_repository->insertMasiveModulesChildrenTenant($dto_childrens_tenant);

            //=========== PLAN ==========
            $this->s_repository->deleteTenantPlans($tenant_data->database);
            $dto_plan_tenant    =   $this->s_dto->getDtoPlanTenant($data['plan_id']);
            $this->s_repository->storePlanTenant($dto_plan_tenant);

            //======== USER PASSWORD ==========
            $dto_user_tenant            =   $this->s_dto->getDtoUpdateUserTenant($data);
            $user_tenant                =   $this->s_repository->updateUserAdminTenant($dto_user_tenant);

            //========= COMPANY ==========
            $dto_company_tenant         =   $this->s_dto->getDtoTenantCompanyUpdate($data);
            $company_tenant             =   $this->s_repository->updateCompanyTenant($dto_company_tenant, 1);

            //============= COMPANY INVOICE TENANT ===========
            $dto_tenant_company_invoice =   $this->s_dto->getDtoTenantCompanyInvoice($data, $company_tenant);
            $company_invoice_tenant     =   $this->s_repository->updateCompanyInvoiceTenant($dto_tenant_company_invoice);

            DB::connection('tenant')->commit();

            Tenant::forgetCurrent();
            return  (object)[
                'company_invoice_tenant'   =>  $company_invoice_tenant,
                'company_tenant' => $company_tenant
            ];
        } catch (Throwable $th) {

            DB::connection('tenant')->rollBack();
            Tenant::forgetCurrent();
            throw $th;
        }
    }

    public function updateLogo(array $data, $tenant_data, Company $company_landlord, Tenant $tenant, TenantCompany $company_tenant)
    {
        $logo       =   $data['logo'] ?? null;
        $logo_url   =   null;
        $logo_name  =   null;
        if ($logo) {

            //========= ELIMINAR LOGO PREVIO ===========
            if ($tenant_data->logo_url) {
                UtilController::deleteFile($tenant_data->logo_url);
            }

            UtilController::saveFileFromLandlord($logo, 'logo_principal', $tenant_data->files_route . '/logo');

            $logo_name  =   'logo_principal.' . $logo->getClientOriginalExtension();
            $logo_url   =   'storage/' . $tenant_data->files_route . '/logo/' . $logo_name;
        } else {

            //========= ELIMINAR LOGO PREVIO ===========
            if ($tenant_data->logo_url) {
                UtilController::deleteFile($tenant_data->logo_url);
            }
        }

        $this->s_repository->saveLogoLandlord($company_landlord, $logo_url, $logo_name);

        $tenant->makeCurrent();
        $this->s_repository->saveLogoTenant($company_tenant, $logo_url, $logo_name);
        Tenant::forgetCurrent();
    }

    public function updateCertificate($tenant_data, array $data, Tenant $tenant, CompanyInvoice $company_invoice_landlord, TenantCompanyInvoice $company_invoice_tenant)
    {
        $certificate   =   $data['certificate'] ?? null;
        if ($certificate) {
            //========= ELIMINAR CERTIFICATE PREVIO ===========
            if ($tenant_data->certificate_url) {
                UtilController::deleteFile($tenant_data->certificate_url);
            }

            $this->saveCertificate($data, $tenant, $company_invoice_landlord, $company_invoice_tenant);
        }
    }

    public function blockAccount(array $data, int $id)
    {
        $tenant_data        =   $this->s_repository->getTenantCompanyData($id);
        $tenant             =   $this->s_repository->findTenant($tenant_data->tenant_id);
        $company_landlord   =   null;

        DB::connection('landlord')->beginTransaction();
        try {
            $company_landlord   =   $this->s_repository->blockAccountLandlord($id, $data['block_account']);

            DB::connection('landlord')->commit();
        } catch (Throwable $e) {
            DB::connection('landlord')->rollBack();
            throw $e;
        }

        $tenant->makeCurrent();
        DB::connection('tenant')->beginTransaction();
        try {

            $this->s_repository->blockAccountTenant($data['block_account']);
            DB::connection('tenant')->commit();

            Cache::forget("company_status_" . $tenant->id);

            Tenant::forgetCurrent();
        } catch (Throwable $e) {
            DB::connection('tenant')->rollBack();
            throw $e;
        }

        return $company_landlord;
    }

    public function dataTestTenant()
    {
        $this->createUserWithRole(
            'CAJERO 1',
            'cajero@gmail.com',
            '123456789',
            'CAJERO',
            2
        );
        for ($i = 1; $i <= 20; $i++) {
            $this->createUserWithRole(
                "MESERO {$i}",
                "mesero{$i}@gmail.com",
                '123456789',
                'MESERO',
                3
            );
        }
        $this->createUserWithRole(
            'CONTADOR',
            'contador@gmail.com',
            '123456789',
            'CONTADOR',
            4
        );
        $this->createUserWithRole(
            'COCINERO',
            'cocinero@gmail.com',
            '123456789',
            'COCINERO',
            5
        );


        //app(TestService::class)->createTestData();
    }

    public function createUserWithRole(
        string $name,
        string $email,
        string $password,
        string $roleName,
        int $positionId
    ): void {
        $collaborator = new Collaborator();
        $collaborator->full_name                  = $name;
        $collaborator->document_type_id           = 1;
        $collaborator->document_number            = rand(70000000, 79999999);
        $collaborator->address                    = 'DIRECCION DEMO';
        $collaborator->phone                      = '9' . rand(10000000, 99999999);
        $collaborator->work_days                  = 30;
        $collaborator->rest_days                  = 20;
        $collaborator->monthly_salary             = 1500;
        $collaborator->daily_salary               = 50;
        $collaborator->position_id                = $positionId;
        $collaborator->document_type_abbreviation = 'DNI';
        $collaborator->save();

        $user = new User();
        $user->name             = strtoupper($name);
        $user->email            = $email;
        $user->password         = Hash::make($password);
        $user->password_visible = $password;
        $user->collaborator_id  = $collaborator->id;
        $user->save();

        $role = Role::where('name', $roleName)->firstOrFail();
        $user->assignRole($role);
    }
}
