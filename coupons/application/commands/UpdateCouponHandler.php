<?php
namespace Coupons\application\commands;

use Coupons\domains\contracts\ICouponRepository;

class UpdateCouponHandler
{
    private $repository;
    private $updateCoupon;

    public function __construct(ICouponRepository $repository, UpdateCoupon $updateCoupon)
    {
        $this->repository = $repository;
        $this->updateCoupon = $updateCoupon;
    }

    public function handle($id, array $data)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Coupon not found'];
        }
        $updated = $this->updateCoupon::execute($existing->toArray(), $data);
        $this->repository->update($id, $updated);
        $fresh = $this->repository->findById($id);
        return ['success' => true, 'coupon' => $fresh ?? $updated];
    }
}
