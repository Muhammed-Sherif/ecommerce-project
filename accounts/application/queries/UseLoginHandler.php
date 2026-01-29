<?php
namespace accounts\application\queries;

use accounts\domains\contracts\Iuser;
use accounts\application\queries\checkUserCrediants;
use accounts\domains\contracts\ISanctumToken;

class UseLoginHandler {
    private $userRepository;
    private $checkUserCrediants;
    private $sanctumToken;
    
    public function __construct(Iuser $userRepository, checkUserCrediants $checkUserCrediants, ISanctumToken $sanctumToken  ) {
        $this->userRepository = $userRepository;
        $this->checkUserCrediants = $checkUserCrediants;
        $this->sanctumToken = $sanctumToken;
    }

    public function handle(array $data) {  
        // Validate input
        if (empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Email and password are required'];
            }
            
            // Check user credentials
            $crediantCheck = $this->checkUserCrediants->check($data);
        if (isset($crediantCheck['success']) && !$crediantCheck['success']) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        //  Generate Sanctum token
        $accessToken = $this->sanctumToken->createToken($crediantCheck['user']['id'], 'api-token', ['*']);
        return [
            'success' => true, 
            'message' => 'Login successful', 
            'user' => isset($crediantCheck['user']) ? $crediantCheck['user'] : null,
            'access_token' => $accessToken,
            'token_type' => 'Bearer'
        ];
    }
}
