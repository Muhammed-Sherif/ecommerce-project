<?php
namespace Coupons\domains\contracts;

interface ICouponApi
{
    public function incrementUsedCount($couponId);
}
