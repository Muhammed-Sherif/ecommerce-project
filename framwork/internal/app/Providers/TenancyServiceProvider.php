<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenancyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (class_exists(BelongsToTenant::class)) {
            BelongsToTenant::$tenantIdColumn = 'user_id';
        }
    }
}
