<?php
namespace accounts\application\commands;

use accounts\domains\contracts\Iuser;
use accounts\application\commands\UpdateUser ;
class UpdateUserHandler {
    private $userRepository;
    private $updateUser;

    public function __construct(Iuser $userRepository , UpdateUser $updateUser) {
        $this->userRepository = $userRepository;
        $this->updateUser = $updateUser;
    }

    public function handle($id, array $data) {
        $user = $this->userRepository->findById($id)->toArray();
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        $updated = $this->updateUser->update($user, $data);
        $this->userRepository->update($id, $data );
        return ['success' => true, 'message' => 'User updated successfully', 'user' => $updated];
    }
}
