<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Fortify;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class CustomAuthenticateUser
{
    public function __invoke(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                Fortify::username() => __('auth.failed'),
            ]);
        }

        if ($user->status === 'ANULADO') {
            throw ValidationException::withMessages([
                Fortify::username() => ['Tu cuenta ha sido anulada. Contacta al administrador.'],
            ]);
        }

        return $user;
    }
}
