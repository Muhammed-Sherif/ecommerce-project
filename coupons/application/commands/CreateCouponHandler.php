<?php
namespace Coupons\application\commands;

use Coupons\domains\contracts\ICouponRepository;

class CreateCouponHandler
{
    private $repository;
    private $createCoupon;

    public function __construct(ICouponRepository $repository, CreateCoupon $createCoupon)
    {
        $this->repository = $repository;
        $this->createCoupon = $createCoupon;
    }

    public function handle(array $data)
    {
        $payload = $this->createCoupon::execute($data);
        $existing = $this->repository->findByCode($payload['code']);
        if ($existing) {
            return ['success' => false, 'message' => 'Coupon code already exists'];
        }
        $id = $this->repository->create($payload);
        $created = $this->repository->findById($id);
        return ['success' => true, 'coupon' => $created ?? $payload];
    }
}
