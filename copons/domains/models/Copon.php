<?php
namespace copons\domains\models;

class Copon
{
    public $id;
    public $code;
    public $discountType;
    public $discountValue;
    public $minOrder;
    public $maxUses;
    public $status;
    public $expiresAt;

    public function __construct(
        $id,
        string $code,
        string $discountType,
        float $discountValue,
        float $minOrder,
        int $maxUses,
        string $status,
        $expiresAt
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->discountType = $discountType;
        $this->discountValue = $discountValue;
        $this->minOrder = $minOrder;
        $this->maxUses = $maxUses;
        $this->status = $status;
        $this->expiresAt = $expiresAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['code'] ?? '',
            $data['discount_type'] ?? 'percent',
            (float) ($data['discount_value'] ?? 0),
            (float) ($data['min_order'] ?? 0),
            (int) ($data['max_uses'] ?? 0),
            $data['status'] ?? 'active',
            $data['expires_at'] ?? null
        );
    }
}
