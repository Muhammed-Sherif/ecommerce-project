<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class InitializeTenancyByUser
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && !empty($user->vendor_id) && function_exists('tenancy')) {
            tenancy()->initialize($user->vendor_id);
        }

        return $next($request);
    }
}
