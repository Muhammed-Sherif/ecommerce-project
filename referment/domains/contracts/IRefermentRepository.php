<?php
namespace referment\domains\contracts;

interface IRefermentRepository
{
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findById($id);
    public function getAll();
    public function getByUser($userId);
    public function findByCode($code);
}
