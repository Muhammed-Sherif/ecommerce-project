<?php
namespace accounts\domains\models;
class UserPassword {
    public $password;
    public static function of($password): UserPassword {
        $instance = new self();
        $instance->password = $password;
        if (!$instance->checkPassword()) {
            throw new \InvalidArgumentException("Invalid password: " . $password);
        }
        return $instance;
    }
    private function checkPassword() {
        return preg_match('/^(?=.*[A-Za-z])(?=.*\d).{10,}$/', $this->password);
    }
}