<?php
namespace Coupons\application\queries;
use Coupons\domains\contracts\ICouponRepository; 
class GetCouponByCode
{
    private $repository;
    private $getCoupon;

    public function __construct(ICouponRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($code)
    {
        $normalized = strtoupper(trim((string) $code));
        if ($normalized === '') {
            return ['success' => false, 'message' => 'Coupon not found'];
        }
        $coupon = $this->repository->findByCode($normalized);
        if (!$coupon) {
            return ['success' => false, 'message' => 'Coupon not found'];
        }
        
        return ['success' => true, 'coupon' => $coupon];
    }
}
