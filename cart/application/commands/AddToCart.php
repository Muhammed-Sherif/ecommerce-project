<?php
namespace cart\application\commands;

class AddToCart
{
    public static function execute(array $data): array
    {
        if (empty($data['product_id'])) {
            throw new \InvalidArgumentException('Product id is required');
        }
        $quantity = isset($data['quantity']) ? (int) $data['quantity'] : 1;
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than zero');
        }
        return [
            'product_id' => $data['product_id'],
            'quantity' => $quantity,
            'coupon_id' => isset($data['coupon_id']) ? (int) $data['coupon_id'] : null,
            'coupon_code' => isset($data['coupon_code']) ? (string) $data['coupon_code'] : null,
        ];
    }
}
