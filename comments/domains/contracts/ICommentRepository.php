<?php
namespace comments\domains\contracts;

interface ICommentRepository
{
    public function create(array $commentData);
    public function update($id, array $commentData);
    public function delete($id);
    public function findById($id);
    public function getAll();
    public function getByProduct($productId);
    public function getByUser($userId);
}
