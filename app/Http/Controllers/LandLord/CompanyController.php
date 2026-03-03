<?php

namespace App\Http\Controllers\LandLord;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\Landlord\Maintenance\Company\CompanyUpdateRequest;
use App\Http\Services\Landlord\Maintenance\Company\CompanyManager;
use App\Models\Department;
use App\Models\District;
use App\Models\Landlord\Company as LandlordCompany;
use App\Models\Module;
use App\Models\Plan;
use App\Models\Province;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Throwable;

class CompanyController extends Controller
{
    private CompanyManager $s_manager;

    public function __construct()
    {
        $this->middleware('auth');
        $this->s_manager    =   new CompanyManager();
    }

    public function index()
    {
        // $companies = DB::table('companies as e')
        //     ->join('tenants as t', 'e.tenant_id', 't.id')
        //     ->join('plans as p','p.id','e.plan')
        //     ->select('e.id', 'e.ruc', 'e.business_name', 'e.created_at', 't.id', 't.domain',
        //     'p.description as plan_name','e.email','e.invoicing_status')
        //     ->get();

        return view('company.landlord');
    }


    public function getCompanies(Request $request)
    {

        $companies = DB::table('companies as e')
            ->join('tenants as t', 'e.tenant_id', 't.id')
            ->join('plans as p', 'p.id', 'e.plan')
            ->select(
                'e.id',
                'e.ruc',
                'e.business_name',
                'e.created_at',
                't.id as tenant_id',
                't.domain',
                'p.description as plan_name',
                'e.email',
                'e.invoicing_status',
                'e.block_account'
            )
            ->where('status', '1')
            ->get();

        return DataTables::of($companies)->make(true);
    }


    public function create(): View
    {
        $all_modules    =   Module::with('children.grandchildren')->get();
        $departments    =   Department::all();
        $provinces      =   Province::all();
        $districts      =   District::all();

        $plans = Plan::select(
            'id',
            'description',
            'price',
            DB::raw('CASE WHEN number_fields > 6 THEN "SIN LÍMITE" ELSE number_fields END AS number_fields'),
        )->get();

        return view('company.create', compact(
            'all_modules',
            'plans',
            'departments',
            'provinces',
            'districts'
        ));
    }

    public function edit($id)
    {
        try {
            $view   =   $this->s_manager->edit($id);
            return $view;
        } catch (Throwable $th) {
            Session::flash('message_error', $th->getMessage());
            return back();
        }
    }


    /*
array:19 [ // app\Http\Controllers\LandLord\CompanyController.php:251
  "_token" => "sULo2DcbohGSxVete8SndX56VouOen08BAlGsU4p"
  "domain" => "a"
  "ruc" => "a"
  "estado" => "SIN VERIFICAR"
  "razon_social" => "a"
  "razon_social_abreviada" => "a"
  "direccion_fiscal" => null
  "department" => "1"
  "province" => "102"
  "district" => "10204"
  "correo" => "admin@gmail.com"
  "password" => "123456789"
  "secondary_user" => null
  "secondary_password" => null
  "certificate_password" => null
  "plan_id" => "1"
  "certificate":File
  "certificate_password":string
  "api_user_gre"
  "api_pass_gree"
  "module_id" => array:12 [
    0 => "1"
    1 => "2"
  ]
  "child_id" => array:37 [
    0 => "1"
    1 => "2"
  ]
  "logo" =>Illuminate\Http\UploadedFile {#2265}
]
*/
    public function store(CompanyStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $tenant =   $this->s_manager->store($request->toArray());

            Session::flash('message_success', 'EMPRESA REGISTRADA CON ÉXITO');

            return response()->json(['success' => true, 'message' => 'EMPRESA REGISTRADA CON ÉXITO']);
        } catch (Throwable $th) {

            DB::connection('landlord')->rollback();

            if (isset($tenant)) {
                DB::connection('landlord')->statement("DROP DATABASE IF EXISTS `{$tenant->database}`");
            }

            Session::flash('message_error', $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
        }
    }

    /*
array:21 [ // app\Http\Controllers\LandLord\CompanyController.php:250
  "_token" => "HWhvHXpuXQ7xMQI8wcOOeWeAaRxhNAB4RWDf0c1u"
  "_method" => "PUT"
  "ruc" => "20609678047"
  "estado" => "SIN VERIFICAR"
  "razon_social" => "TU RESTAURANTE"
  "razon_social_abreviada" => "TU RESTAURANTE"
  "direccion_fiscal" => "TU RESTAURANTE"
  "department" => "13"
  "province" => "1301"
  "district" => "130101"
  "correo" => "admin@gmail.com"
  "password" => "123456789"
  "secondary_user" => "SOLUSER"
  "secondary_password" => "SOLPASS"
  "api_user_gre" => "SOLGRE"
  "api_pass_gre" => "PASSGRE"
  "certificate":File
  "certificate_password" => null
  "plan_id" => "3"
  "module_id" => array:12 [
    0 => "1"
    1 => "2"
  ]
  "child_id" => array:37 [
    0 => "1"
    1 => "2"
    2 => "3"
  ]
  "logo" =>Illuminate\Http\UploadedFile {#2225}
]
*/
    public function update(CompanyUpdateRequest $request, $id)
    {
        try {

            $this->s_manager->update($request->toArray(), $id);

            Session::flash('message_success', 'EMPRESA ACTUALIZADA CON ÉXITO');

            return response()->json(['success' => true, 'message' => 'EMPRESA ACTUALIZADA CON ÉXITO']);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
        }
    }


    /*
array:1 [ // app\Http\Controllers\LandLord\CompanyController.php:263
  "company_id" => "1"
]
*/
    public function resetearClave(Request $request)
    {
        DB::beginTransaction();
        try {

            $company_id     =   $request->get('company_id');

            $tenant_data    =   DB::select('select
                                c.ruc,
                                t.database
                                from tenants as t
                                inner join companies as c on c.tenant_id = t.id
                                where c.id = ?', [$company_id])[0];


            DB::table("$tenant_data->database.users as u")
                ->where('u.id', '1')
                ->update(['u.password' => Hash::make($tenant_data->ruc)]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'CLAVE RESETEADA CON ÉXITO!!!']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function deleteTenant($id)
    {

        try {

            //====== OBTENER EMPRESA =======
            $company = LandlordCompany::findOrFail($id);

            if (!$company) {
                throw new Exception("NO EXISTE LA EMPRESA EN LA BD!!");
            }

            //====== OBTENER DATOS DEL TENANT =======
            $tenant_data = DB::select('select
                                            c.ruc,
                                            t.database
                                            from tenants as t
                                            inner join companies as c on c.tenant_id = t.id
                                            where c.id = ?', [$id])[0];

            //======== VERIFICAR SI EXISTE LA BD DEL TENANT =======
            $exists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$tenant_data->database]);
            if (!$exists) {
                throw new Exception("NO EXISTE LA BD DEL TENANT!!");
            }

            //===== ELIMINAR LA BD DEL TENANT =======
            DB::statement("DROP DATABASE IF EXISTS {$tenant_data->database}");

            //======= ELIMINAR ARCHIVOS DEL TENANT ======
            $path_directory_tenant = public_path('storage/' . $company->files_route);
            if (File::exists($path_directory_tenant) && File::isDirectory($path_directory_tenant)) {
                File::deleteDirectory($path_directory_tenant);
            }

            //====== DESACTIVAR LA EMPRESA ========
            $company->status = '0';
            $company->update();

            return response()->json(['success' => true, 'message' => 'EMPRESA ELIMINADA!!!']);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function blockAccount(Request $request, $id)
    {
        try {

            $company_landlord   =   $this->s_manager->blockAccount($request->toArray(), $id);
            $message            =   $company_landlord->block_account ? 'EMPRESA BLOQUEADA CON ÉXITO' : 'EMPRESA ACTIVADA CON ÉXITO';

            return response()->json(['success' => true, 'message' => $message]);
        } catch (Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
        }
    }
}
