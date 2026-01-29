<?php
namespace inventory\domains\contracts;

interface IStockMovementRepository
{
    public function create(array $movementData);
    public function findByInventory($inventoryId);
    public function getAll(array $filters = []);
}
