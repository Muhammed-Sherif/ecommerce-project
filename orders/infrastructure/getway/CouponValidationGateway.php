<?php
namespace orders\infrastructure\getway;

use orders\domains\contracts\ICouponValidationGateway;
use Coupons\shared\CouponValidationApi;

class CouponValidationGateway implements ICouponValidationGateway
{
    private $couponApi;

    public function __construct(CouponValidationApi $couponApi)
    {
        $this->couponApi = $couponApi;
    }

    public function validateByCode($code)
    {
        return $this->couponApi->validateByCode($code);
    }
}
