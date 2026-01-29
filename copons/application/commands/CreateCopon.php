<?php
namespace copons\application\commands;

class CreateCopon
{
    public static function execute(array $data): array
    {
        if (empty($data['code'])) {
            throw new \InvalidArgumentException('Copon code is required');
        }
        if (empty($data['discount_type'])) {
            throw new \InvalidArgumentException('Discount type is required');
        }
        if (!isset($data['discount_value'])) {
            throw new \InvalidArgumentException('Discount value is required');
        }

        $type = $data['discount_type'];
        $value = (float) $data['discount_value'];
        if ($value < 0) {
            throw new \InvalidArgumentException('Discount value must be zero or positive');
        }
        if (!in_array($type, ['percent', 'fixed'], true)) {
            throw new \InvalidArgumentException('Discount type must be percent or fixed');
        }
        if ($type === 'percent' && $value > 100) {
            throw new \InvalidArgumentException('Percent discount cannot exceed 100');
        }

        return [
            'code' => strtoupper(trim($data['code'])),
            'discount_type' => $type,
            'discount_value' => $value,
            'min_order' => isset($data['min_order']) ? (float) $data['min_order'] : 0,
            'max_uses' => isset($data['max_uses']) ? (int) $data['max_uses'] : 0,
            'status' => $data['status'] ?? 'active',
            'expires_at' => $data['expires_at'] ?? null,
        ];
    }
}
