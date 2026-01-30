<?php
namespace Coupons\application\commands;

class DeleteCoupon
{
    public static function execute($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Coupon id is required');
        }
        return $id;
    }
}
