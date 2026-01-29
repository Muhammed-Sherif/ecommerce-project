<?php
namespace cart\application\commands;

class RemoveCartItem
{
    public static function execute(array $data): array
    {
        if (empty($data['product_id'])) {
            throw new \InvalidArgumentException('Product id is required');
        }
        return ['product_id' => $data['product_id']];
    }
}
