<?php   
namespace accounts\domains\models;
use accounts\domains\models\UserEmail;   
class User {
    public $id;
    public $name;
    public $email;
    public $passwordHash;
    public $status;
    public $role;

    public function __construct($id = null , $name, $email, $passwordHash, $status , $role = 'customer') {
        $this->id = $id ?? null;
        $this->name = $name;
        $this->email = UserEmail::of($email);
        $this->passwordHash = UserPassword::of($passwordHash);
        $this->status = $status;
        $this->role = $role;
    }
}