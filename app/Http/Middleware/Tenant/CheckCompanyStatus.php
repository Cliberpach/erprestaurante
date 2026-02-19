<?php

namespace App\Http\Middleware\Tenant;

use App\Models\Tenant\Maintenance\Company\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCompanyStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $company    =   Company::findOrFail(1);

        if ($company->block_account) {
            return response()->view('tenant.errors.company-status', [], 403);
        }

        return $next($request);
    }
}
