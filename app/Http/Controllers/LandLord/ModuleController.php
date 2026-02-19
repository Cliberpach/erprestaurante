<?php

namespace App\Http\Controllers\LandLord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use App\Models\ModuleChild;
use App\Models\ModuleGrandchild;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function home()
    {
        return view('dashboard');
    }

    public function logout(Request $request)
    {
        // Cerrar la sesión del usuario autenticado
        Auth::guard('web')->logout();

        // Invalida la sesión actual
        $request->session()->invalidate();

        // Regenera el token CSRF para evitar problemas de seguridad
        $request->session()->regenerateToken();

        // Redirige al usuario a la página de inicio de sesión
        return redirect('/login');
    }
}
