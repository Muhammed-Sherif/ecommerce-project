<?php
namespace Coupons\application\commands;

class CreateCoupon
{
    public static function execute(array $data): array
    {
        if (empty($data['code'])) {
            throw new \InvalidArgumentException('Coupon code is required');
        }
        $type = $data['type'] ?? $data['discount_type'] ?? 'fixed';
        $rawValue = $data['value'] ?? $data['discount'] ?? $data['discount_value'] ?? null;
        if ($rawValue === null) {
            throw new \InvalidArgumentException('Discount value is required');
        }

        $type = strtolower(trim($type));
        if ($type === 'amount') {
            $type = 'fixed';
        } elseif ($type === 'percent') {
            $type = 'percentage';
        }

        $value = (float) $rawValue;
        if ($value < 0) {
            throw new \InvalidArgumentException('Discount value must be zero or positive');
        }
        if (!in_array($type, ['percentage', 'fixed'], true)) {
            throw new \InvalidArgumentException('Discount type must be percentage or fixed');
        }
        if ($type === 'percentage' && $value > 100) {
            throw new \InvalidArgumentException('Percent discount cannot exceed 100');
        }

        return [
            'code' => strtoupper(trim($data['code'])),
            'type' => $type,
            'value' => $value,
            'min_order_amount' => isset($data['min_order_amount'])
                ? (float) $data['min_order_amount']
                : (isset($data['min_order']) ? (float) $data['min_order'] : 0),
            'usage_limit' => isset($data['usage_limit'])
                ? (int) $data['usage_limit']
                : (isset($data['max_uses']) ? (int) $data['max_uses'] : 0),
            'is_active' => isset($data['is_active'])
                ? (bool) $data['is_active']
                : (isset($data['status']) ? $data['status'] === 'active' : true),
            'expires_at' => $data['expires_at'] ?? null,
        ];
    }
}
