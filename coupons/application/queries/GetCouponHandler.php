<?php
namespace Coupons\application\queries;

use Coupons\domains\contracts\ICouponRepository;

class GetCouponHandler
{
    private $repository;
    private $getCoupon;

    public function __construct(ICouponRepository $repository, GetCoupon $getCoupon)
    {
        $this->repository = $repository;
        $this->getCoupon = $getCoupon;
    }

    public function handle($id)
    {
        // Check authentication first
        if (!auth()->check()) {
            return ['success' => false, 'message' => 'Authentication required for accessing coupons'];
        }
        
        $coupon = $this->repository->findById($id);
        if (!$coupon) {
            return ['success' => false, 'message' => 'Coupon not found or access denied'];
        }
        return ['success' => true, 'coupon' => $this->getCoupon::execute($coupon)];
    }
}
