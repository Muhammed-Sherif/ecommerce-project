<?php

namespace shared;

use Illuminate\Support\Facades\Auth;

class TenantContext
{
    public static function id(): ?int
    {
        if (function_exists('tenant')) {
            $tenantId = tenant('id');
            if ($tenantId !== null && $tenantId !== '') {
                return (int) $tenantId;
            }
        }

        $user = Auth::user();
        return $user && isset($user->vendor_id) ? (int) $user->vendor_id : null;
    }
}
