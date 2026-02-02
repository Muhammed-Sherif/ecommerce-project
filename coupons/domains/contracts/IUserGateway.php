<?php
namespace Coupons\domains\contracts;

interface IUserGateway
{
    /**
     * Find user by ID
     * @param int $userId
     * @return object|null
     */
    public function findById($userId);
}