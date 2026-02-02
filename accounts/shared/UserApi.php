<?php
namespace accounts\shared;

use accounts\application\queries\GetUserHandler;
use accounts\domains\contracts\IUserApi;

class UserApi implements IUserApi
{
    private $getUserHandler;

    public function __construct(GetUserHandler $getUserHandler)
    {
        $this->getUserHandler = $getUserHandler;
    }

    /**
     * Find user by ID
     * @param int $userId
     * @return object|null
     */
    public function findById($userId)
    {
        $result = $this->getUserHandler->handle($userId);
        if ($result && isset($result["success"]) && $result["success"]) {
            return $result["user"] ?? null;
        }
        return null;
    }
}