<?php
namespace inventory\domains\contracts;

interface IInventoryRepository
{
    public function create(array $inventoryData);
    public function update($id, array $inventoryData);
    public function findById($id);
    public function findByProduct($productId);
    public function getAll(array $filters = []);
    public function reserve($productId, int $quantity);
    public function release($productId, int $quantity);
    public function adjustStock($productId, int $quantity, string $reason);
}
