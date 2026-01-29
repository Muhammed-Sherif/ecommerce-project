<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use shared\events\CheckoutCompleted;
use orders\application\listeners\CreateOrderListener;
use inventory\application\listeners\ReserveStockListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CheckoutCompleted::class => [
            CreateOrderListener::class,
            ReserveStockListener::class,
        ],
        \shared\events\OrderCreated::class => [
            \payments\application\listeners\InitiatePaymentListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
