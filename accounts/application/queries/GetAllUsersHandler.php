<?php
namespace accounts\application\queries;

use accounts\domains\contracts\Iuser;

class GetAllUsersHandler {
    private $userRepository;
    private $getAllUsers;

    public function __construct(Iuser $userRepository, GetAllUsers $getAllUsers) {
        $this->userRepository = $userRepository;
        $this->getAllUsers = $getAllUsers;
    }

    public function handle() {
        $users = $this->userRepository->getAll()->toArray();
        $users = $this->getAllUsers::execute($users ?? []);
        return ['success' => true, 'users' => $users];
    }
}
