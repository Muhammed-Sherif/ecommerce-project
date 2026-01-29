<?php
namespace accounts\application\commands;

class UpdateUser {
    public static function execute($existingUser, array $data) {
        // Allow updating selected fields
        if (isset($data['name'])) {
            if (is_array($existingUser)) {
                $existingUser['name'] = $data['name'];
            } else {
                $existingUser->name = $data['name'];
            }
        }
        if (isset($data['email'])) {
            if (is_array($existingUser)) {
                $existingUser['email'] = $data['email'];
            } else {
                $existingUser->email = $data['email'];
            }
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $hash = password_hash($data['password'], PASSWORD_BCRYPT);
            if (is_array($existingUser)) {
                $existingUser['password'] = $hash;
            } else {
                $existingUser->password = $hash;
            }
        }
        if (is_array($existingUser)) {
            $existingUser['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $existingUser->updated_at = date('Y-m-d H:i:s');
        }
        return $existingUser;
    }
}
