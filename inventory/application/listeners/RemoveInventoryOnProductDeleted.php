<?php
namespace inventory\application\listeners;

use shared\events\ProductDeleted;
use inventory\domains\contracts\IInventoryRepository;

class RemoveInventoryOnProductDeleted
{
    private $inventoryRepository;

    public function __construct(IInventoryRepository $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function handle(ProductDeleted $event)
    {
        try {
            $this->inventoryRepository->deleteByProduct($event->productId);
        } catch (\Throwable $e) {
            error_log('Failed to remove inventory for product ' . $event->productId);
        }
    }
}
