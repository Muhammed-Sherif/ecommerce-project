<?php
namespace accounts\application\commands;

use accounts\domains\contracts\Iuser;

class DeleteUserHandler {
    private $userRepository;
    private $deleteUser;

    public function __construct(Iuser $userRepository, DeleteUser $deleteUser) {
        $this->userRepository = $userRepository;
        $this->deleteUser = $deleteUser;
    }

    public function handle($id) {
        $user = $this->userRepository->findById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        // there is no logic in DeleteUser.php now, so we can call it but it does nothing
        $this->deleteUser::execute($user);
        $this->userRepository->delete($id);
        return ['success' => true, 'message' => 'User deleted successfully'];
    }
}
