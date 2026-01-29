<?php
namespace inventory\application\commands;

class AdjustStock
{
    public static function execute(array $data): array
    {
        if (empty($data['product_id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }

        if (!isset($data['quantity'])) {
            throw new \InvalidArgumentException('Quantity is required');
        }

        if (empty($data['reason'])) {
            throw new \InvalidArgumentException('Reason is required');
        }

        return [
            'product_id' => $data['product_id'],
            'quantity' => (int) $data['quantity'],
            'reason' => trim($data['reason']),
            'created_by' => $data['created_by'] ?? null,
        ];
    }
}
