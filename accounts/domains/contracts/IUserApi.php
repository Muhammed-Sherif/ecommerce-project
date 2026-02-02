<?php
namespace accounts\domains\contracts;

interface IUserApi
{
    /**
     * Find user by ID
     * @param int $userId
     * @return array|null
     */
    public function findById($userId);
}