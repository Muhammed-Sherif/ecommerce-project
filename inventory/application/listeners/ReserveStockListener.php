<?php
namespace inventory\application\listeners;

use shared\events\CheckoutCompleted;
use inventory\application\commands\ReserveStockHandler;

class ReserveStockListener
{
    private $reserveStockHandler;

    public function __construct(ReserveStockHandler $reserveStockHandler)
    {
        $this->reserveStockHandler = $reserveStockHandler;
    }

    public function handle(CheckoutCompleted $event)
    {
        $data = $event->checkoutData;
        
        foreach ($data['items'] as $item) {
            try {
                $this->reserveStockHandler->handle([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity']
                ]);
            } catch (\Exception $e) {
                error_log("Failed to reserve stock for product " . $item['product_id']);
                // In a real system, you might want to compensate/rollback the order or flag it
            }
        }
    }
}
