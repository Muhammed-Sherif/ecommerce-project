<?php
namespace orders\application\commands;

class CancelOrder
{
    public static function execute(array $data): array
    {
        if (empty($data['order_id'])) {
            throw new \InvalidArgumentException('Order ID is required');
        }

        return [
            'order_id' => $data['order_id'],
        ];
    }
}
