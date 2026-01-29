<?php
namespace orders\domains\models;

class OrderItem
{
    public $id;
    public $orderId;
    public $productId;
    public $productName;
    public $quantity;
    public $unitPrice;
    public $totalPrice;
    public $createdAt;
    public $updatedAt;

    public function __construct(
        $id = null,
        $orderId = null,
        int $productId,
        string $productName,
        int $quantity,
        float $unitPrice,
        $createdAt = null,
        $updatedAt = null
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->totalPrice = $unitPrice * $quantity;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['order_id'] ?? null,
            $data['product_id'],
            $data['product_name'],
            (int) ($data['quantity']),
            (float) ($data['unit_price']),
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null
        );
    }

    public static function calculateTotalPrice(int $quantity, float $unitPrice): float
    {
        return $quantity * $unitPrice;
    }
}
