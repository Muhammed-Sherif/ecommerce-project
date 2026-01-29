<?php
namespace inventory\application\commands;

class ReserveStock
{
    public static function execute(array $data): array
    {
        if (empty($data['product_id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }

        if (!isset($data['quantity']) || $data['quantity'] <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero');
        }

        return [
            'product_id' => $data['product_id'],
            'quantity' => (int) $data['quantity'],
        ];
    }
}
