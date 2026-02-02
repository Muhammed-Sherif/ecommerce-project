<?php
namespace products\application\commands;

class CreateProduct
{
    public static function execute(array $product): array
    {
        if (empty($product['name'])) {
            throw new \InvalidArgumentException('Product name is required');
        }
        if (!isset($product['price'])) {
            throw new \InvalidArgumentException('Product price is required');
        }

        $price = (float) $product['price'];
        $quantity = isset($product['quantity']) ? (int) $product['quantity'] : 0;

        if ($price < 0) {
            throw new \InvalidArgumentException('Price must be zero or positive');
        }
        if (array_key_exists('stock', $product)) {
            throw new \InvalidArgumentException('Use quantity instead of stock');
        }
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity must be zero or positive');
        }
        if (empty($product['user_id'])) {
            throw new \InvalidArgumentException('User is required');
        }

        $payload = [
            'name' => trim($product['name']),
            'description' => $product['description'] ?? '',
            'price' => $price,
            'category' => $product['category'] ?? 'general',
            'status' => $product['status'] ?? 'active',
            'images' => $product['images'] ?? null,
        ];

        $payload['user_id'] = $product['user_id'];

        return $payload;
    }
}
