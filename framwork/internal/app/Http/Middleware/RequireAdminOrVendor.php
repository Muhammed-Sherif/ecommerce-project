<?php

namespace App\Http\Middleware;

use Closure;

class RequireAdminOrVendor
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $status = strtolower((string) ($user->status ?? ''));
        if ($status !== '' && $status !== 'active' && $status !== '1') {
            return response()->json(['success' => false, 'message' => 'Account inactive'], 403);
        }

        if (!in_array($user->role, ['admin', 'vendor'], true) ) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}