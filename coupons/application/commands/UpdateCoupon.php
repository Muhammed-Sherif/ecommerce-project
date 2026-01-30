<?php
namespace Coupons\application\commands;

class UpdateCoupon
{
    public static function execute(array $existing, array $data): array
    {
        $updated = $existing;

        if (isset($data['code'])) {
            $updated['code'] = strtoupper(trim($data['code']));
        }
        if (isset($data['type']) || isset($data['discount_type'])) {
            $type = strtolower(trim($data['type'] ?? $data['discount_type']));
            if ($type === 'amount') {
                $type = 'fixed';
            } elseif ($type === 'percent') {
                $type = 'percentage';
            }
            if (!in_array($type, ['percentage', 'fixed'], true)) {
                throw new \InvalidArgumentException('Discount type must be percentage or fixed');
            }
            $updated['type'] = $type;
        }
        if (isset($data['value']) || isset($data['discount']) || isset($data['discount_value'])) {
            $rawValue = $data['value'] ?? $data['discount'] ?? $data['discount_value'];
            $value = (float) $rawValue;
            if ($value < 0) {
                throw new \InvalidArgumentException('Discount value must be zero or positive');
            }
            if (($updated['type'] ?? 'percentage') === 'percentage' && $value > 100) {
                throw new \InvalidArgumentException('Percent discount cannot exceed 100');
            }
            $updated['value'] = $value;
        }
        if (isset($data['min_order_amount']) || isset($data['min_order'])) {
            $updated['min_order_amount'] = isset($data['min_order_amount'])
                ? (float) $data['min_order_amount']
                : (float) $data['min_order'];
        }
        if (isset($data['usage_limit']) || isset($data['max_uses'])) {
            $updated['usage_limit'] = isset($data['usage_limit'])
                ? (int) $data['usage_limit']
                : (int) $data['max_uses'];
        }
        if (isset($data['is_active']) || isset($data['status'])) {
            $updated['is_active'] = isset($data['is_active'])
                ? (bool) $data['is_active']
                : ($data['status'] === 'active');
        }
        if (array_key_exists('expires_at', $data)) {
            $updated['expires_at'] = $data['expires_at'];
        }

        return $updated;
    }
}
