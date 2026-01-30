<?php

return [
    'tenant_model' => App\Models\Tenant::class,
    'domain_model' => Stancl\Tenancy\Database\Models\Domain::class,
    'id_generator' => null,

    'central_domains' => [
        parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost',
    ],

    // Single-database tenancy: DatabaseTenancyBootstrapper intentionally removed.
    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    ],

    'features' => [],
];
