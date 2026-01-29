<?php
namespace orders\application\commands;

use orders\domains\models\OrderStatus;

class UpdateOrderStatus
{
    public static function execute(array $data): array
    {
        if (empty($data['order_id'])) {
            throw new \InvalidArgumentException('Order ID is required');
        }

        if (empty($data['status'])) {
            throw new \InvalidArgumentException('Status is required');
        }

        $newStatus = $data['status'];

        // Validate status
        if (!OrderStatus::isValid($newStatus)) {
            throw new \InvalidArgumentException(
                'Invalid status. Valid statuses are: ' . 
                implode(', ', OrderStatus::getValidStatuses())
            );
        }

        return [
            'order_id' => $data['order_id'],
            'status' => $newStatus,
        ];
    }
}
