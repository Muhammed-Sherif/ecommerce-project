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

        $payload = [ 
            "name"=>$user['name'],
            "email"=>$user['email'],
            "role"=>$user['role'] ?? 'customer',
            "password"=>password_hash($user['password'], PASSWORD_BCRYPT),
            "status"=>UserStatus::get($user, $userSentRequest)
        ];
        if (array_key_exists('vendor_id', $user)) {
            $payload['vendor_id'] = $user['vendor_id'];
        } elseif ($userSentRequest && isset($userSentRequest->vendor_id)) {
            $payload['vendor_id'] = $userSentRequest->vendor_id;
        }
        return $payload;
    }
}
