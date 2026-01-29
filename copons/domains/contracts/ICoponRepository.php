<?php
namespace copons\domains\contracts;

interface ICoponRepository
{
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findById($id);
    public function getAll();
    public function findByCode($code);
}
