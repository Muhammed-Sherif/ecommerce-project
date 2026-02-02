<?php
namespace Coupons\infrastructure\getway;

use Coupons\domains\contracts\IUserGateway;
use accounts\shared\UserApi;

class UserGateway implements IUserGateway
{
    private $userApi;

    public function __construct(UserApi $userApi)
    {
        $this->userApi = $userApi;
    }

    /**
     * Find user by ID - Gateway only handles user communication
     * @param int $userId
     * @return object|null
     */
    public function findById($userId)
    {
        return $this->userApi->findById($userId);
    }
}