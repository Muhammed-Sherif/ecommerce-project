<?php
namespace accounts\application\commands;
use accounts\domains\models\UserStatus;
use accounts\domains\contracts\Iuser;
use accounts\domains\models\User;
class CreateUser {
    private $userRepository;
    public function __construct( Iuser $userRepository ) {
        $this->userRepository = $userRepository;
    }

    public static function execute($user , $userSentRequest = null) {

        $user = [ 
            "name"=>$user['name'],
            "email"=>$user['email'],
            "role"=>$user['role'] ?? 'customer',
            "password"=>password_hash($user['password'], PASSWORD_BCRYPT),
            "status"=>UserStatus::get($user, $userSentRequest)
        ];
        return $user;
    }
}