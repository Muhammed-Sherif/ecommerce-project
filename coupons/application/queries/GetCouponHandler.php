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
        $coupon = $this->repository->findById($id);
        if (!$coupon) {
            return ['success' => false, 'message' => 'Coupon not found'];
        }
        return ['success' => true, 'coupon' => $this->getCoupon::execute($coupon)];
    }
}
