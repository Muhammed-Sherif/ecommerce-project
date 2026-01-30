<?php
namespace Coupons\application\commands;

use Coupons\domains\contracts\ICouponRepository;

class DeleteCouponHandler
{
    private $repository;
    private $deleteCoupon;

    public function __construct(ICouponRepository $repository, DeleteCoupon $deleteCoupon)
    {
        $this->repository = $repository;
        $this->deleteCoupon = $deleteCoupon;
    }

    public function handle($id)
    {
        $couponId = $this->deleteCoupon::execute($id);
        $existing = $this->repository->findById($couponId);
        if (!$existing) {
            return ['success' => false, 'message' => 'Coupon not found'];
        }
        $this->repository->delete($couponId);
        return ['success' => true, 'message' => 'Coupon deleted'];
    }
}
