<?php
namespace accounts\infrastructure\repositries;

use accounts\domains\contracts\Iuser;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class IuserImp implements Iuser {

    // Create
    public function create(array $userData) {
        $user = User::query()->create($userData);
        return $user->id;
    }

    // Read - Get all users
    public function getAll() {
        $query = User::query();
        return $query->get();
    }

    // Read - Find user by ID
    public function findById($id) {
        $query = User::query()->where('id', $id);
        return $query->first();
    }
        
    // Read - Find user by email
    public function findByEmail($email) {
        return User::query()->where('email', $email)->first();
    }

    // Update
    public function update($id, $userData) {
        if (is_object($userData)) {
            $userData = (array) $userData;
        }
        return User::where('id', $id)->first()->update($userData);
    }

    // Delete
    public function delete($id) {
        $query = User::query()->where('id', $id);
        return $query->delete();
    }

    public function getCurrentUser() {
        return Auth::user();
    }   
}
