<?php

namespace App\Http\Controllers\Tenant\Maintenance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Maintenance\Roles\RolStoreRequest;
use App\Http\Requests\Tenant\Maintenance\Roles\RolUpdateRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;
use Spatie\Multitenancy\Models\Tenant;

class RoleController extends Controller
{
    public function index()
    {
        return view('maintenance.roles.index');
    }

    public function create()
    {
        $permissions = Permission::where('status', 'ACTIVO')->get();
        return view('maintenance.roles.create', compact('permissions'));
    }

    public function getAll(Request $request)
    {
        $roles = Role::from('roles as r')
            ->where('r.status', 'ACTIVO')
            ->select(
                'r.id',
                'r.name',
                'r.created_at'
            );

        return DataTables::of($roles)
            ->editColumn('created_at', function ($role) {
                return $role->created_at
                    ->timezone('America/Lima')
                    ->format('Y-m-d H:i:s');
            })
            ->make(true);
    }

    public function edit($id)
    {

        //========== OBTENER EL ROL Y SUS PERMISOS ======
        $rol                    =   Role::find($id);
        $permisos               =   Permission::where('status', 'ACTIVO')->get();
        $permisos_asignados     =   DB::select('SELECT * from role_has_permissions as rhp
                                    where rhp.role_id = ?', [$id]);

        return view('maintenance.roles.edit', compact('rol', 'permisos', 'permisos_asignados'));
    }


    public function store(RolStoreRequest $request)
    {
        DB::connection('tenant')->beginTransaction();
        try {

            $lstPermisosAsignados   =   json_decode($request->get('lstPermisosAsignados'));

            $rol                =   new Role();
            $rol->setConnection('tenant');
            $rol->name          =   mb_strtoupper(trim($request->get('nombre')), 'UTF-8');
            $rol->guard_name    =   'web';
            $rol->save();

            //======== INSERTANDO PERMISOS =========
            foreach ($lstPermisosAsignados as $permiso) {
                DB::insert(
                    'insert into role_has_permissions (permission_id, role_id) values (?, ?)',
                    [$permiso, $rol->id]
                );
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'ROL REGISTRADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function update(RolUpdateRequest $request, $id)
    {
        DB::connection('tenant')->beginTransaction();
        try {
            dd('r');
            $lstPermisosAsignados   =   json_decode($request->get('lstPermisosAsignados'));

            $rol                =   Role::find($id);
            $rol->setConnection('tenant');
            $rol->name          =   mb_strtoupper(trim($request->get('nombre')), 'UTF-8');
            $rol->guard_name    =   'web';
            $rol->update();

            //======== ELIMINANDO PERMISOS PREVIOS ====
            DB::delete('DELETE FROM role_has_permissions
            WHERE role_id = ?', [$id]);

            //======== INSERTANDO PERMISOS =========
            foreach ($lstPermisosAsignados as $permiso) {
                DB::insert(
                    'insert into role_has_permissions (permission_id, role_id) values (?, ?)',
                    [$permiso, $rol->id]
                );
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();
            $tenantId = Tenant::current()?->id ?? 'landlord';
            $user = auth()->user()->load('roles');
            $roleNames = $user->roles->pluck('name')->implode('_');
            Cache::forget("menu_{$tenantId}_{$roleNames}");

            DB::commit();
            return response()->json(['success' => true, 'message' => 'ROL ACTUALIZADO CON ÉXITO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::connection('tenant')->beginTransaction();
        try {

            $rol                    =   Role::findOrFail($id);
            $rol->status            =   'ANULADO';
            $rol->update();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'ROL ELIMINADO']);
        } catch (Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $th->getMessage()]);
        }
    }
}
