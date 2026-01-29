<?php
namespace orders\application\listeners;

use shared\events\CheckoutCompleted;
use orders\application\commands\CreateOrderHandler;

class CreateOrderListener
{
    private $createOrderHandler;

    public function __construct(CreateOrderHandler $createOrderHandler)
    {
        $this->createOrderHandler = $createOrderHandler;
    }

    public function handle(CheckoutCompleted $event)
    {
        $data = $event->checkoutData;
        
        // Map checkout data to Order creation data
        // Assuming $data contains 'user_id', 'items', 'shipping_address', etc.
        
        try {
            $this->createOrderHandler->handle([
                'customer_id' => $data['user_id'],
                'items' => $data['items'],
                'shipping_street' => $data['shipping_address']['street'],
                'shipping_city' => $data['shipping_address']['city'],
                'shipping_state' => $data['shipping_address']['state'],
                'shipping_country' => $data['shipping_address']['country'],
                'shipping_zip_code' => $data['shipping_address']['zip_code'],
            ]);
        } catch (\Exception $e) {
            // Log error or fire 'OrderCreationFailed' event
            // For now, we'll just log
            error_log("Failed to create order from checkout: " . $e->getMessage());
        }
    }
}
