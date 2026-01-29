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
        $stock = isset($product['stock']) ? (int) $product['stock'] : 0;

        if ($price < 0) {
            throw new \InvalidArgumentException('Price must be zero or positive');
        }
        if ($stock < 0) {
            throw new \InvalidArgumentException('Stock must be zero or positive');
        }

        return [
            'name' => trim($product['name']),
            'description' => $product['description'] ?? '',
            'price' => $price,
            'category' => $product['category'] ?? 'general',
            'status' => $product['status'] ?? 'active',
            'image' => $product['image'] ?? null,
        ];
    }
}
