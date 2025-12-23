<?php

namespace App\Http\Controllers\Tenant\Maintenance;

use App\Http\Controllers\Controller;
use App\Http\Services\Tenant\Maintenance\User\UserManager;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    private UserManager $s_manager;

    public function __construct()
    {
        $this->s_manager   =   new UserManager();
    }

    public function index()
    {
        return view('user');
    }

    public function store()
    {
        User::create([
            'name' => 'Jhon Livias',
            'email' => 'jlivias@gmail.com',
            'password' => Hash::make('123123qwe'),
        ]);

        return back();
    }

    public function getListFreeServers ()
    {
        $meseros = $this->s_manager->getMeserosLibres();
        return DataTables::of($meseros)->toJson();
    }
}
