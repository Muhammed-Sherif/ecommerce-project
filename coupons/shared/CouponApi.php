<?php
namespace Coupons\shared;

use Coupons\domains\contracts\ICouponApi;
use Coupons\domains\contracts\ICouponRepository;

class CouponApi implements ICouponApi
{
    private $repository;

    public function __construct(ICouponRepository $repository)
    {
        $this->repository = $repository;
    }

    public function incrementUsedCount($couponId)
    {
        return $this->repository->incrementUsedCount($couponId);
    }
}
