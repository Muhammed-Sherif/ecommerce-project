<?php
namespace accounts\infrastructure\repositries;

use accounts\domains\contracts\Iuser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
class IuserImp implements Iuser {

    // Create
    public function create(array $userData) {
        return DB::table('users')->insert($userData);
    }

    // Read - Get all users
    public function getAll() {
        return DB::table('users')->get();
    }

    // Read - Find user by ID
    public function findById($id) {
        return DB::table('users')->where('id', $id)->first();
    }
        
    // Read - Find user by email
    public function findByEmail($email) {
        return DB::table('users')->where('email', $email)->first();
    }

    // Update
    public function update($id, $userData) {
        if (is_object($userData)) {
            $userData = (array) $userData;
        }
        return DB::table('users')
            ->where('id', $id)
            ->update($userData);
    }

    // Delete
    public function delete($id) {
        return DB::table('users')->where('id', $id)->delete();
    }

    public function getCurrentUser() {
        return Auth::user();
    }   
}
