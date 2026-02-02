<?php
namespace orders\infrastructure\getway;

use orders\domains\contracts\ICouponGateway;
use Coupons\shared\CouponApi;

class CouponGateway implements ICouponGateway
{
    private $couponApi;

    public function __construct(CouponApi $couponApi)
    {
        $this->couponApi = $couponApi;
    }

    public function incrementUsedCount($couponId)
    {
        return $this->couponApi->incrementUsedCount($couponId);
    }
}
