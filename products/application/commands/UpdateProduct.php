<?php
namespace products\application\commands;

class UpdateProduct
{
    public static function execute(array $existing, array $data): array
    {
        $updated = $existing;

        if (isset($data['name'])) {
            if (trim($data['name']) === '') {
                throw new \InvalidArgumentException('Product name cannot be empty');
            }
            $updated['name'] = trim($data['name']);
        }

        if (array_key_exists('description', $data)) {
            $updated['description'] = $data['description'];
        }

        if (isset($data['price'])) {
            $price = (float) $data['price'];
            if ($price < 0) {
                throw new \InvalidArgumentException('Price must be zero or positive');
            }
            $updated['price'] = $price;
        }

        if (isset($data['category'])) {
            $updated['category'] = $data['category'];
        }

        if (isset($data['status'])) {
            $updated['status'] = $data['status'];
        }

        if (array_key_exists('image', $data)) {
            $updated['image'] = $data['image'];
        }

        return $updated;
    }
}
