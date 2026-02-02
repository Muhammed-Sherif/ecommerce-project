<?php
namespace orders\application\commands;

class CancelOrder
{
    public static function execute(array $data): array
    {
        // in here the logic such as return the shipping and refund and other and can be implemtned later
        if (empty($data['order_id'])) {
            throw new \InvalidArgumentException('Order ID is required');
        }

        return [
            'order_id' => $data['order_id'],
        ];
    }
}
