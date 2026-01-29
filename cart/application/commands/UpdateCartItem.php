<?php
namespace cart\application\commands;

class UpdateCartItem
{
    public static function execute(array $data): array
    {
        if (empty($data['product_id'])) {
            throw new \InvalidArgumentException('Product id is required');
        }
        if (!isset($data['quantity'])) {
            throw new \InvalidArgumentException('Quantity is required');
        }
        $quantity = (int) $data['quantity'];
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero');
        }
        return [
            'product_id' => $data['product_id'],
            'quantity' => $quantity,
        ];
    }
}
