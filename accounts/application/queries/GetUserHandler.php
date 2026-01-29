<?php
namespace accounts\application\queries;

use accounts\domains\contracts\Iuser;

class GetUserHandler {
    private $userRepository;
    private $getUser;

    public function __construct(Iuser $userRepository, GetUser $getUser) {
        $this->userRepository = $userRepository;
        $this->getUser = $getUser;
    }

    public function handle($id) {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        $user = $this->getUser::execute($user);
        return ['success' => true, 'user' => $user];
    }
}
