<?php

namespace App\Http\Controllers\Tenant;


use App\Http\Controllers\Controller;
use App\Http\Controllers\UtilController;
use App\Http\Requests\Company\CompanyNumerationRequest;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\Tenant\Maintenance\Company\CompanyInvoiceRequest;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Department;
use App\Models\District;
use App\Models\Landlord\GeneralTable\GeneralTableDetail;
use App\Models\Module;
use App\Models\ModuleChild;
use App\Models\ModuleGrandChild;
use App\Models\Province;
use App\Models\Tenant;
use App\Models\Tenant\DocumentSerialization;
use App\Models\Tenant\Maintenance\Company\CompanyInvoice;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private $modules;
    private $children;
    private $grand_children;

    public function index()
    {
        $companies = DB::table('companies as e')
            ->select('e.id', 'e.ruc', 'e.business_name', 'e.created_at')
            ->get();

        return view('company.tenant', compact('companies'));
    }

    public function create(): View
    {
        return view('company.create');
    }

    public function edit($id)
    {
        $company            =   Company::findOrFail($id);

        $departments        =   DB::select('select * from departments');
        $districts          =   DB::select('select * from districts');
        $provinces          =   DB::select('select * from provinces');

        $company_invoice    =   CompanyInvoice::where('company_id', $company->id)->get()[0];

        $billing_documents  =   UtilController::getInvoiceTypesFree();
        return view(
            'company.editcompanie_tenant',
            compact(
                'company',
                'departments',
                'districts',
                'provinces',
                'company_invoice',
                'billing_documents'
            )
        );
    }

    public function store(CompanyStoreRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $domain = strtolower($request->get("domain"));
            $tenant = Tenant::create([
                "name" => $request->input('razon_social'),
                "domain" => $domain . "." . parse_url(config("app.url"), PHP_URL_HOST),
            ]);

            $company = new Company();
            $company->tenant_id = $tenant->id;
            $company->ruc = $request->get("ruc");
            $company->business_name = $request->get("razon_social");
            $company->abbreviated_business_name  = $request->get("razon_social_abreviada");
            $company->zip_code = $request->get("ubigeo");
            $company->fiscal_address = $request->get("direccion_fiscal");
            $company->email = $request->get("correo");

            if ($request->hasFile('certificate_url')) {
                $imagen = $request->file('certificate_url');
                $fileFolderPath = 'assets/img/certificado/';
                $nombreImagen = $imagen->getClientOriginalName();
                $suffix = 1;
                $fileNameWithoutExtension = pathinfo($nombreImagen, PATHINFO_FILENAME);
                while (CompanyInvoice::where('certificate_url', $nombreImagen)->exists()) {
                    $fileName = $fileNameWithoutExtension . "($suffix)." . $imagen->getClientOriginalExtension();
                    $suffix++;
                    $nombreImagen = $fileName;
                }
                $imagen->move(public_path($fileFolderPath), $nombreImagen);
                $company->certificate = $nombreImagen;
                $company->certificate_url = $fileFolderPath . $nombreImagen;
            }

            $company->save();

            $module_array = $request->module_id;
            $child_array = $request->child_id;
            $grandchild_array = $request->grandchild_id;

            $this->modules = Module::whereIn('id', $module_array)->get();
            $this->children = ModuleChild::whereIn('id', $child_array)->get();
            $this->grand_children = ModuleGrandChild::whereIn('id', $grandchild_array)->get();

            DB::commit();

            $this->insertDataTenant($tenant->database, $request);

            return to_route("landlord.mantenimientos.empresa");
        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->back()->with("error", $ex->getMessage());
        }
    }

    private function insertDataTenant($database, $request)
    {
        DB::statement("use $database");

        $company = new Company();
        $company->ruc = $request->get("ruc");
        $company->business_name = $request->get("razon_social");
        $company->abbreviated_business_name  = $request->get("razon_social_abreviada");
        $company->zip_code = $request->get("ubigeo");
        $company->fiscal_address = $request->get("direccion_fiscal") ? $request->get('direccion_fiscal') : 'NO INDICADO';
        $company->email = $request->get("correo");

        if ($request->hasFile('certificate_url')) {
            $imagen = $request->file('certificate_url');
            $fileFolderPath = 'assets/img/certificado/';
            $nombreImagen = $imagen->getClientOriginalName();
            $suffix = 1;
            $fileNameWithoutExtension = pathinfo($nombreImagen, PATHINFO_FILENAME);
            while (CompanyInvoice::where('certificate_url', $nombreImagen)->exists()) {
                $fileName = $fileNameWithoutExtension . "($suffix)." . $imagen->getClientOriginalExtension();
                $suffix++;
                $nombreImagen = $fileName;
            }
            $imagen->move(public_path($fileFolderPath), $nombreImagen);
            $company->certificate = $nombreImagen;
            $company->certificate_url = $fileFolderPath . $nombreImagen;
        }

        $company->save();

        $user = new User();
        $user->name = 'SUPERADMIN';
        $user->email = $request->get("correo");
        $user->password = Hash::make($request->get("password"));
        $user->save();

        $role = Role::where('name', 'admin')->first();
        $user->assignRole($role);

        DB::table("document_serializations")->insert([
            ['company_id' => $company->id, 'document_type_id' => '01', 'serie' => 'F001', 'number_limit' => 8, 'destiny' => 'VENTAS', 'default' => 'NO', 'final_number' => 0],
            ['company_id' => $company->id, 'document_type_id' => '03', 'serie' => 'B001', 'number_limit' => 8, 'destiny' => 'VENTAS', 'default' => 'NO', 'final_number' => 0],
            ['company_id' => $company->id, 'document_type_id' => '07', 'serie' => 'FC01', 'number_limit' => 8, 'destiny' => 'FNC', 'default' => 'NO', 'final_number' => 0],
            ['company_id' => $company->id, 'document_type_id' => '08', 'serie' => 'FD01', 'number_limit' => 8, 'destiny' => 'FND', 'default' => 'NO', 'final_number' => 0],
            ['company_id' => $company->id, 'document_type_id' => '09', 'serie' => 'T001', 'number_limit' => 8, 'destiny' => 'GUIAS', 'default' => 'NO', 'final_number' => 0],
            ['company_id' => $company->id, 'document_type_id' => '80', 'serie' => 'NV01', 'number_limit' => 8, 'destiny' => 'VENTAS', 'default' => 'NO', 'final_number' => 0],
            ['company_id' => $company->id, 'document_type_id' => '50', 'serie' => 'TV01', 'number_limit' => 8, 'destiny' => 'VENTAS', 'default' => 'SI', 'final_number' => 0],
            ['company_id' => $company->id, 'document_type_id' => '52', 'serie' => 'NI01', 'number_limit' => 8, 'destiny' => 'NOTAS', 'default' => 'NO', 'final_number' => 0],
            ['company_id' => $company->id, 'document_type_id' => '53', 'serie' => 'NS01', 'number_limit' => 8, 'destiny' => 'NOTAS', 'default' => 'NO', 'final_number' => 0],
        ]);

        foreach ($this->modules as $module) {
            Module::create([
                'id' => $module->id,
                'description' => $module->description,
                'order' => $module->order,
            ]);
        }

        foreach ($this->children as $children) {
            ModuleChild::create([
                'id' => $children->id,
                'module_id' => $children->module_id,
                'description' => $children->description,
                'route_name' => $children->route_name,
                'order' => $children->order,
            ]);
        }

        foreach ($this->grand_children as $grand_children) {
            ModuleGrandChild::create([
                'id' => $grand_children->id,
                'module_child_id' => $grand_children->module_child_id,
                'description' => $grand_children->description,
                'route_name' => $grand_children->route_name,
                'order' => $grand_children->order,
            ]);
        }
    }

    /*
array:16 [▼ // app\Http\Controllers\Tenant\CompanyController.php:223
  "_token"                      => "t1wIO5GKzEziM9RdaOMIWJLeOl8rAFQzis778ha6"
  "_method"                     => "PUT"
  "ruc"                         => "20370146994"
  "business_name"               => "CORPORACION ACEROS AREQUIPA S.A."
  "abbreviated_business_name"   => "CORPORACION ACEROS AREQUIPA S.A."
  "fiscal_address"              => null
  "phone"                       => null
  "cellphone"                   => null
  "zip_code"                    => "13001"
  "email"                       => "admin@gmail.com"
  "facebook"                    => null
  "instagram"                   => null
  "web"                         => null
  "invoicing_status"            => "0"
  "lat"                         => "-8.105881685888642"
  "lng"                         => "-79.0307748666481"
  "department"                  => "22"
  "province"                    => "2208"
  "district"                    => "220807"
]
*/
    public function update(Request $request, $id)
    {

        //========= GUARDANDO UBIGEO =====
        $company_invoice                    =   CompanyInvoice::find(1);
        $company_invoice->department_id     =   $request->get('department');
        $company_invoice->province_id       =   $request->get('province');
        $company_invoice->district_id       =   $request->get('district');

        $department     =   Department::findOrFail($request->get('department'));
        $province       =   Province::findOrFail($request->get('province'));
        $district       =   District::findOrFail($request->get('district'));

        $company_invoice->department_name   =   $department->name;
        $company_invoice->province_name     =   $province->name;
        $company_invoice->district_name     =   $district->name;
        $company_invoice->update();


        $company = Company::findOrFail($id);

        // Validar el formulario, incluyendo la validación de archivo
        $request->validate([
            'business_name' => 'required|string|max:255',
            'abbreviated_business_name' => 'nullable|string|max:255',
            'fiscal_address'    => 'nullable|string|max:255',
            'phone'             => 'nullable|string|max:20',
            'cellphone'         => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:255',
            'zip_code'          => 'nullable|string|max:10',
            'facebook'          => 'nullable|string|max:255',
            'instagram'         => 'nullable|string|max:255',
            'web'               => 'nullable|string|max:255',
            'invoicing_status'  => 'required|in:0,1',
            'logo'              => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048', // Validación del logo
            'base64_logo'       => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {

            $route_logo_tenant   =   public_path('storage/' . $company->files_route . '/logo/');

            if (!File::exists($route_logo_tenant)) {
                File::makeDirectory($route_logo_tenant, 0755, true);
            }

            //======= ELIMINAR LOGO ANTERIOR SI EXISTE =======
            if (File::exists($company->logo_url)) {
                File::delete($company->logo_url);
            }

            $file                   =   $request->file('logo');
            $fileName               =   $company->ruc . '.' . $file->getClientOriginalExtension();

            $base64_logo            =   'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
            $company->base64_logo   =   $base64_logo;
            $company->logo          =   $fileName;
            $company->logo_url      =   'storage/' . $company->files_route . '/logo/' . $fileName;

            $file->move($route_logo_tenant, $fileName);
        }

        // Guardar los demás campos
        $company->business_name             =   $request->business_name;
        $company->abbreviated_business_name =   $request->abbreviated_business_name;
        $company->fiscal_address            =   $request->fiscal_address;
        $company->phone                     =   $request->phone;
        $company->cellphone                 =   $request->cellphone;
        $company->email                     =   $request->email;
        $company->zip_code                  =   $request->zip_code;
        $company->facebook                  =   $request->facebook;
        $company->instagram                 =   $request->instagram;
        $company->web                       =   $request->web;
        $company->invoicing_status          =   $request->invoicing_status;
        $company->lat                       =   $request->get('lat');
        $company->lng                       =   $request->get('lng');
        $company->igv                       =   $request->get('igv');
        $company->save();

        return redirect()->route('tenant.mantenimiento.empresas.index')->with('success', 'Empresa actualizada correctamente');
    }


    /*
array:11 [ // app\Http\Controllers\Tenant\CompanyController.php:254
  "_token"          => "msn2RI2Bm4Zyz8grzFO3PrR1HEZEBK0dDYU7YchN"
  "urbanization"    => "afasf"  --REQEST
  "local_code"      => "0000"   --REQUEST
  "sol_user"        => "asfaf"  --REQUEST
  "sol_pass"        => "asfasfas"   --REQUEST
  "api_user_gre"    => null         --REQUEST
  "api_pass_gre"   => null         --REQUEST
  "certificate"     => Illuminate\Http\UploadedFile {#1964} -REQUEST
  "certificate_password"
]
*/
    public function updateInvoice($id, CompanyInvoiceRequest $request)
    {
        DB::beginTransaction();

        try {

            $company_invoice                    =   CompanyInvoice::find(1);
            $company_invoice->secondary_user    =   $request->get('sol_user');
            $company_invoice->secondary_password =  $request->get('sol_pass');
            $company_invoice->ubigeo            =   $request->get('district');
            $company_invoice->urbanization      =   $request->get('urbanization');
            $company_invoice->local_code        =   $request->get('local_code');
            $company_invoice->api_user_gre      =   $request->get('api_user_gre');
            $company_invoice->api_password_gre  =   $request->get('api_pass_gre');
            $company_invoice->certificate_password  =   $request->get('certificate_password');
            $company_invoice->update();

            //========= PREGUNTANDO SI HAY CERTIFICADO EN EL REQUEST ========
            if ($request->hasFile('certificate')) {

                $certificateFile    = $request->file('certificate');
                $extension          = strtolower($certificateFile->getClientOriginalExtension());
                if (!in_array($extension, ['pem', 'p12'])) {
                    throw ValidationException::withMessages([
                        'certificate' => [
                            'El certificado debe tener extensión .pem o .p12.'
                        ]
                    ]);
                }

                if ($extension === 'p12' && !$request->filled('certificate_password')) {
                    throw ValidationException::withMessages([
                        'certificate_password' => [
                            'La contraseña del certificado es obligatoria cuando se sube un archivo P12.'
                        ]
                    ]);
                }

                $company            = Company::find(1);
                $pemFilename        = 'cert_production.pem';
                $directoryPath      = $company->files_route . '/greenter/certs/';
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
                        throw new \Exception('No se pudo guardar el archivo P12 temporal');
                    }

                    $p12Password = $request->input('certificate_password');

                    // 1️⃣ Extraer CLAVE PRIVADA
                    $cmdPrivateKey = $opensslPath . ' pkcs12 -legacy ' .
                        '-in ' . escapeshellarg($tempP12Path) . ' ' .
                        '-nocerts -nodes ' .
                        '-out ' . escapeshellarg($privateKeyPath) . ' ' .
                        '-password pass:' . escapeshellarg($p12Password) . ' 2>&1';

                    exec($cmdPrivateKey, $outKey, $codeKey);

                    if ($codeKey !== 0) {
                        logger()->error('Error extrayendo clave privada', $outKey);
                        throw new \Exception('Error al extraer la clave privada del P12');
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
                        throw new \Exception('Error al extraer el certificado del P12');
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


                $company_invoice->certificate_url   = 'storage/' . $company->files_route . '/greenter/certs/' . $pemFilename;
                $company_invoice->certificate       = $pemFilename;
                $company_invoice->update();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'DATOS DE FACTURACIÓN ACTUALIZADOS']);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
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

    public function getListNumeration(Request $request)
    {

        $numerations = DB::table('document_serializations as ds')
            ->select(
                'ds.id',
                'ds.serie',
                'ds.start_number',
                'ds.description',
                'ds.initiated'
            )
            ->get();

        return DataTables::of($numerations)
            ->make(true);
    }

    /*
array:3 [ // app\Http\Controllers\Tenant\CompanyController.php:395
  "billing_type_document"   => "3"
  "serie"                   => "B001"
  "start_number"            => "1"
]
*/
    public function storeNumeration(CompanyNumerationRequest $request)
    {
        DB::beginTransaction();

        try {

            //====== VALIDANDO QUE NO EXISTA NUMERACIÓN PREVIA PARA ESTE TIPO DE DOCUMENTO =========
            $numeration_exists  =   DocumentSerialization::where('document_type_id', $request->get('billing_type_document'))->first();

            if ($numeration_exists) {
                throw new Exception("ESTE DOCUMENTO YA TIENE NUMERACIÓN!!!");
            }

            //======== OBTIENDO DATA DEL TIPO DE DOCUMENTO =========
            $type_document  =   GeneralTableDetail::findOrFail($request->get('billing_type_document'));

            $serialization                      =   new DocumentSerialization();
            $serialization->company_id          =   1;
            $serialization->document_type_id    =   $type_document->id;
            $serialization->serie               =   $request->get('serie');
            $serialization->description         =   $type_document->description;
            $serialization->start_number        =   $request->get('start_number');
            $serialization->number_limit        =   8;
            $serialization->destiny             =   null;
            $serialization->default             =   'NO';
            $serialization->final_number        =   0;
            $serialization->initiated           =   'NO';
            $serialization->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => "NUMERACIÓN REGISTRADA"]);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
