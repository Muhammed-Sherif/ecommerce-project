<?php
namespace Coupons\domains\models;

class CouponRedemption
{
    public $id;
    public $couponId;
    public $userId;
    public $orderId;
    public $amount;
    public $status;
    public $redeemedAt;

    public function __construct(
        $id,
        $couponId,
        $userId,
        $orderId,
        float $amount,
        string $status = 'redeemed',
        $redeemedAt = null
    ) {
        $this->id = $id;
        $this->couponId = $couponId;
        $this->userId = $userId;
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->status = $this->validateStatus() ? 'redeemed' : throw new \Exception("Cannot set status to {$status} for coupon redemption.");
        $this->redeemedAt = $this->validateRedeemedAt() ? $redeemedAt : null;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['coupon_id'] ?? null,
            $data['user_id'] ?? null,
            $data['order_id'] ?? null,
            (float) ($data['amount'] ?? 0),
            $data['status'] ?? 'redeemed',
            $data['redeemed_at'] ?? null
        );
    }
    public function validateRedeemedAt()
    {
        if ($this->redeemedAt && strtotime($this->redeemedAt) > time()) {
            throw new \Exception("Coupon redemption redeemed at cannot be in the future.");
        }
        return true;
    }
    public function validateStatus()
    {
        $valid = ['redeemed'];
        if (!in_array($this->status, $valid)) {
            throw new \Exception("Invalid coupon redemption status: {$this->status}");
        }
        return true;
    }
}
