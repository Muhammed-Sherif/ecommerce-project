<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;

class InitializeTenancyByHeader
{
    public function handle($request, Closure $next)
    {
        $tenantId = $request->header('X-Tenant-ID') ?? $request->header('X-Tenant') ?? $request->query('tenant');
        if (!$tenantId) {
            return $next($request);
        }

        InitializeTenancyByRequestData::$header = 'X-Tenant-ID';
        InitializeTenancyByRequestData::$queryParameter = 'tenant';
        return app(InitializeTenancyByRequestData::class)->handle($request, $next);
    }
}