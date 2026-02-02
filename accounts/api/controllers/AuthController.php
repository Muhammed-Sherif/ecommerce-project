<?php
namespace accounts\api\controllers;

use accounts\domains\contracts\Iuser;
use accounts\application\commands\CreateUserHandler;
use accounts\application\commands\UpdateUserHandler;
use accounts\application\commands\DeleteUserHandler;
use accounts\application\queries\UseLoginHandler;
use accounts\application\queries\GetAllUsersHandler;
use accounts\application\queries\GetUserHandler;
use accounts\domains\contracts\ISanctumToken ;
use accounts\api\requests\UpdateUserRequest;
class AuthController
{
    private $userRepository;
    private $createUserHandler;
    private $loginHandler;
    private $updateUserHandler;
    private $deleteUserHandler;
    private $getAllUsersHandler;
    private $getUserHandler;
    private $sanctumToken;
    public function __construct(
        Iuser $userRepository,
        CreateUserHandler $createUserHandler,
        UseLoginHandler $loginHandler,
        UpdateUserHandler $updateUserHandler,
        DeleteUserHandler $deleteUserHandler,
        GetAllUsersHandler $getAllUsersHandler,
        GetUserHandler $getUserHandler, 
        ISanctumToken $sanctumToken 
    ) {
        $this->userRepository = $userRepository;
        $this->createUserHandler = $createUserHandler;
        $this->loginHandler = $loginHandler;
        $this->updateUserHandler = $updateUserHandler;
        $this->deleteUserHandler = $deleteUserHandler;
        $this->getAllUsersHandler = $getAllUsersHandler;
        $this->getUserHandler = $getUserHandler;
        $this->sanctumToken = $sanctumToken;
    }

    /**
     * Register a new user
     */
    public function register($data)
    {
        if (empty($data['email']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        $currentUser = $this->userRepository->getCurrentUser();
        $result = $this->createUserHandler->handle($data, $currentUser);

        if ($result['success']) {
            return ['success' => true, 'message' => 'User registered successfully', 'user' => $result['user']];
        }
        return ['success' => false, 'message' => 'Registration failed'];
    }

    /**
     * Login user
     */
    public function login($data)
    {
        return $this->loginHandler->handle($data);
    }

    /**
     * Logout user
     */
  public function logout()
    {

        session_unset();
        session_destroy();
        $this->sanctumToken->revokeAllTokens($this->userRepository->getCurrentUser()->id);
        return ['success' => true, 'message' => 'Logout successful'];
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser()
    {
        $user = $this->userRepository->getCurrentUser();
        if (!$user) {
            return ['success' => false, 'message' => 'No authenticated user'];
        }

        return ['success' => true, 'user' => $user];
    }

    /**
     * Update user profile
     */
    public function update($id, UpdateUserRequest $request)
    {
        return $this->updateUserHandler->handle($id, $request->toArray());
    }

    /**
     * Delete user (soft delete via handler)
     */
    public function delete($id)
    {
        return $this->deleteUserHandler->handle($id);
    }

    /**
     * Get all users
     */
    public function getAll()
    {
        return $this->getAllUsersHandler->handle();
    }

    /**
     * Get user by ID
     */
    public function getById($id)
    {
        return $this->getUserHandler->handle($id);
    }
}
