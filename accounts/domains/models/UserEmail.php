<?php
namespace accounts\domains\models;
class UserEmail {
    public $email;
    public static function of($email): UserEmail {
        $instance = new self();
        $instance->email = $email;
        if (!$instance->checkEmail()) {
            throw new \InvalidArgumentException("Invalid email address: " . $email);
        }
        return $instance;
    }
    private function checkEmail() {
        return filter_var($this->email, FILTER_VALIDATE_EMAIL) !== false;
    }
}