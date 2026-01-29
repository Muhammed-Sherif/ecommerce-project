<?php
namespace inventory\domains\models;

class Inventory
{
    public $id;
    public $productId;
    public $quantity;
    public $reservedQuantity;
    public $warehouseLocation;
    public $createdAt;
    public $updatedAt;

    public function __construct(
        $id,
        $productId,
        int $quantity,
        int $reservedQuantity = 0,
        string $warehouseLocation = 'default',
        $createdAt = null,
        $updatedAt = null
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->reservedQuantity = $reservedQuantity;
        $this->warehouseLocation = $warehouseLocation;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['product_id'] ?? null,
            (int) ($data['quantity'] ?? 0),
            (int) ($data['reserved_quantity'] ?? 0),
            $data['warehouse_location'] ?? 'default',
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    public function getAvailableQuantity(): int
    {
        return max(0, $this->quantity - $this->reservedQuantity);
    }

    public function hasStock(int $requestedQuantity = 1): bool
    {
        return $this->getAvailableQuantity() >= $requestedQuantity;
    }

    public function canReserve(int $quantity): bool
    {
        return $this->hasStock($quantity);
    }

    public function canRelease(int $quantity): bool
    {
        return $this->reservedQuantity >= $quantity;
    }

    public function adjustStock(int $quantity): void
    {
        $this->quantity += $quantity;
        if ($this->quantity < 0) {
            $this->quantity = 0;
        }
    }
}
