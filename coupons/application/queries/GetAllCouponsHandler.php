<?php
namespace Coupons\application\queries;

use Coupons\domains\contracts\ICouponRepository;

class GetAllCouponsHandler
{
    private $repository;
    private $getAllCoupons;

    public function __construct(ICouponRepository $repository, GetAllCoupons $getAllCoupons)
    {
        $this->repository = $repository;
        $this->getAllCoupons = $getAllCoupons;
    }

    public function handle()
    {
        $coupons = $this->repository->getAll();
        return ['success' => true, 'coupons' => $this->getAllCoupons::execute($coupons->toArray() ?? [])];
    }
}
