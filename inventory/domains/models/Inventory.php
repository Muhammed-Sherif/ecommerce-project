<?php
namespace inventory\domains\models;

class Inventory
{
    public $id;
    public $productId;
    public $quantity;
    // public $warehouseLocation;
    public $createdAt;
    public $updatedAt;

    public function __construct(
        $id = null,
        $productId,
        int $quantity,
        // string $warehouseLocation = 'default',
        $createdAt = null,
        $updatedAt = null
    ) {
        $this->id = $id ?? null;
        $this->productId = $productId ?: throw new \InvalidArgumentException('Product ID is required');
        $this->quantity = $quantity > 0 ? $quantity : throw new \InvalidArgumentException('Quantity is required and cant be zero or negative') ;
        // $this->warehouseLocation = $warehouseLocation;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['product_id'],
            (int) ($data['quantity']),
            // $data['warehouse_location'] ?? 'default',
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    public function getAvailableQuantity(): int
    {
        return max(0, $this->quantity);
    }

    public function hasStock(int $requestedQuantity = 1): bool
    {
        return $this->getAvailableQuantity() >= $requestedQuantity;
    }

        // public function canReserve(int $quantity): bool
        // {
        //     return $this->hasStock($quantity);
        // }

        // public function canRelease(int $quantity): bool
        // {
        //     return $this->reservedQuantity >= $quantity;
        // }
}
