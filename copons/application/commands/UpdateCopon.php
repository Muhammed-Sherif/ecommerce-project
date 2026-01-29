<?php
namespace copons\application\commands;

class UpdateCopon
{
    public static function execute(array $existing, array $data): array
    {
        $updated = $existing;

        if (isset($data['code'])) {
            $updated['code'] = strtoupper(trim($data['code']));
        }
        if (isset($data['discount_type'])) {
            $type = $data['discount_type'];
            if (!in_array($type, ['percent', 'fixed'], true)) {
                throw new \InvalidArgumentException('Discount type must be percent or fixed');
            }
            $updated['discount_type'] = $type;
        }
        if (isset($data['discount_value'])) {
            $value = (float) $data['discount_value'];
            if ($value < 0) {
                throw new \InvalidArgumentException('Discount value must be zero or positive');
            }
            if (($updated['discount_type'] ?? 'percent') === 'percent' && $value > 100) {
                throw new \InvalidArgumentException('Percent discount cannot exceed 100');
            }
            $updated['discount_value'] = $value;
        }
        if (isset($data['min_order'])) {
            $updated['min_order'] = (float) $data['min_order'];
        }
        if (isset($data['max_uses'])) {
            $updated['max_uses'] = (int) $data['max_uses'];
        }
        if (isset($data['status'])) {
            $updated['status'] = $data['status'];
        }
        if (array_key_exists('expires_at', $data)) {
            $updated['expires_at'] = $data['expires_at'];
        }

        return $updated;
    }
}
