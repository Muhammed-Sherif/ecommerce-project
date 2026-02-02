<?php
namespace products\application\commands;

class UpdateProduct
{
    public static function execute(array $data): array
    {
        if (isset($data['name'])) {
            if (trim($data['name']) === '') {
                throw new \InvalidArgumentException('Product name cannot be empty');
            }
        }

        if (array_key_exists('description', $data)) {
            if (trim($data['description']) === '') {
                throw new \InvalidArgumentException('Product description cannot be empty');
            }
        }

        if (isset($data['price'])) {
            if ($data['price'] < 0) {
                throw new \InvalidArgumentException('Price must be zero or positive');
            }
        }

        if (isset($data['category'])) {
            if (trim($data['category']) === '') {
                throw new \InvalidArgumentException('Product category cannot be empty');
            }
        }

        if (isset($data['status'])) {
            if (!in_array($data['status'], ['inactive', 'active'])) {
                throw new \InvalidArgumentException('Invalid product status');
            }
        }

        if (array_key_exists('images', $data)) {
            if (is_array($data['images']) && count($data['images']) === 0) {
                throw new \InvalidArgumentException('Product images cannot be empty');
            }
            if (is_string($data['images']) && trim($data['images']) === '') {
                throw new \InvalidArgumentException('Product images cannot be empty');
            }
        }
        if (array_key_exists('quantity', $data) && (int) $data['quantity'] < 0) {
            throw new \InvalidArgumentException('Quantity must be zero or positive');
        }

        return $data;
    }
}
