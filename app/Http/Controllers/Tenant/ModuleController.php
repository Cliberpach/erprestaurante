<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ModuleController extends Controller
{
    public function home()
    {

        if (auth()->user()->hasRole('MESERO')) {
            return redirect()->route('tenant.mostrador_mesero.mostrador.index');
        }
        if (auth()->user()->hasRole('CAJERO')) {
            return view('cashier_counter.counter.index');
        }

        if (auth()->user()->can('dashboard.dashboard.index')) {
            return redirect()->route('tenant.dashboard.dashboard.index');
        }
        return view('home');
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
