<?php
namespace accounts\application\commands;

use accounts\domains\contracts\Iuser;
use accounts\application\commands\CreateUser;

class CreateUserHandler {
    private $userRepository;
    private $createUser;
    public function __construct(Iuser $userRepository , CreateUser $createUser) {
        $this->userRepository = $userRepository;
        $this->createUser = $createUser;
        }
        public function handle($userData , $userSentRequest = null) {
            $findByEmail = $this->userRepository->findByEmail($userData['email']);
            if($findByEmail) {
                throw new \Exception("User with this email already exists");
            }
            $user = $this->createUser::execute($userData , $userSentRequest);
            $this->userRepository->create($user);
            return ['success' => true, 'user' => $user];
            // we can add more such as notifications , events , logs etc
        }

}
