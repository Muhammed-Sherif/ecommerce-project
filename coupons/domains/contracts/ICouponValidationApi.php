<?php
namespace Coupons\domains\contracts;

interface ICouponValidationApi
{
    public function validateByCode($code);
}
