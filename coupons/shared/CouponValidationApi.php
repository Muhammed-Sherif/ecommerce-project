<?php
namespace Coupons\shared;

use Coupons\domains\contracts\ICouponValidationApi;
use Coupons\application\queries\CheckValidityOfCouponByCodeHandler;

class CouponValidationApi implements ICouponValidationApi
{
    private $handler;

    public function __construct(CheckValidityOfCouponByCodeHandler $handler)
    {
        $this->handler = $handler;
    }

    public function validateByCode($code)
    {
        return $this->handler->handle($code);
    }
}
