<?php
namespace Coupons\domains\contracts;

interface ICartGateway
{
    /**
     * Get cart for user
     * @param int $userId
     * @return array
     */
    public function getCart($userId);
}