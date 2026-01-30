<?php
namespace Coupons\application\queries;

use Coupons\domains\contracts\ICouponRepository;
use Coupons\application\queries\GetCouponByCode;

class CheckValidityOfCouponByCodeHandler
{
    private $repository;
    private $getCoupon;

    public function __construct(ICouponRepository $repository, GetCouponByCode $getCoupon)
    {
        $this->repository = $repository;
        $this->getCoupon = $getCoupon;
    }

    public function handle($code)
    {
        $result = $this->getCoupon->handle($code);
        if (!$result['success']) {
            return ['success' => false, 'message' => 'Coupon not found'];
        }
        $coupon = $result['coupon'];
        if (!$coupon->is_active) {
            return ['success' => false, 'message' => 'Coupon is not active'];
        }
        if ($coupon->usage_limit > 0 && $coupon->used_count >= $coupon->usage_limit) {
            return ['success' => false, 'message' => 'Coupon redemption limit reached'];
        }
        if ($coupon->expires_at) {
            $expiresAt = $coupon->expires_at instanceof \DateTimeInterface
                ? $coupon->expires_at->getTimestamp()
                : strtotime((string) $coupon->expires_at);
            if ($expiresAt && $expiresAt < time()) {
                return ['success' => false, 'message' => 'Coupon has expired'];
            }
        }

        return ['success' => true, 'message' => 'Coupon is valid', 'coupon' => $coupon];
    }
}
