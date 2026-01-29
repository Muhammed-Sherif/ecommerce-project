<?php
namespace accounts\application\queries;

use accounts\domains\contracts\Iuser;

class checkUserCrediants {
    private $userRepository;

    public function __construct(Iuser $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function check(array $data) {  
        // Find user
        $user = $this->userRepository->findByEmail($data['email']);
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid credentials'];
            }
            // Verify password
            if (!password_verify($data['password'], $user->password)) {
                return ['success' => false, 'message' => 'Invalid credentials'];
                }
                
        return [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'role' => $user->role
            ]
        ];
    }
}
