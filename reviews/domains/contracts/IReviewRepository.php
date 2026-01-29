<?php
namespace reviews\domains\contracts;

interface IReviewRepository
{
    public function create(array $reviewData);
    public function update($id, array $reviewData);
    public function delete($id);
    public function findById($id);
    public function findByProduct($productId, array $filters = []);
    public function findByUser($userId);
    public function getAll(array $filters = []);
    public function getAverageRating($productId);
}
