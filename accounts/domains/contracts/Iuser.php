<?php
namespace accounts\domains\contracts;
interface Iuser {
    public function create(array $userData);
    public function update($id, $userData);
    public function delete($id);
    public function getAll();
    public function findByEmail($email);
    public function findById($id);
    public function getCurrentUser();
}
