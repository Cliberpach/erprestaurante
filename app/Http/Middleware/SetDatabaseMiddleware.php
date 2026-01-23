<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class SetDatabaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isTenant = Tenant::checkCurrent();
        $connection = $isTenant ? 'tenant' : 'landlord';
        Config::set('database.default', $connection);
        //dd(config('database.default'));
        return $next($request);
    }
}
