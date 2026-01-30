<?php
namespace Coupons\domains\models;

class Coupon
{
    public $id;
    public $code;
    public $type;
    public $value;
    public $minOrderAmount;
    public $usageLimit;
    public $usedCount;
    public $isActive;
    public $expiresAt;

    public function __construct(
        $id,
        string $code,
        string $type,
        float $value,
        float $minOrderAmount,
        int $usageLimit,
        int $usedCount,
        bool $isActive,
        $expiresAt
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->type = $type;
        $this->value = $value;
        $this->minOrderAmount = $minOrderAmount;
        $this->usageLimit = $usageLimit;
        $this->usedCount = $usedCount;
        $this->isActive = $isActive;
        $this->expiresAt = $expiresAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['code'] ?? '',
            $data['type'] ?? 'fixed',
            (float) ($data['value'] ?? 0),
            (float) ($data['min_order_amount'] ?? 0),
            (int) ($data['usage_limit'] ?? 0),
            (int) ($data['used_count'] ?? 0),
            (bool) ($data['is_active'] ?? true),
            $data['expires_at'] ?? null
        );
    }
}
