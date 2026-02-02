<?php
namespace orders\domains\contracts;

interface ICouponGateway
{
    public function incrementUsedCount($couponId);
}
