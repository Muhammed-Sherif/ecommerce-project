<?php
namespace inventory\application\queries;

class GetInventory
{
    public static function execute(array $data): array
    {
        if (empty($data['product_id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }

        return [
            'product_id' => $data['product_id'],
        ];
    }
}
