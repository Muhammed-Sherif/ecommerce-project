<?php
namespace inventory\infrastructure\repositories;

use inventory\domains\contracts\IStockMovementRepository;
use App\Models\StockMovement;

class StockMovementRepository implements IStockMovementRepository
{
    public function create(array $movementData)
    {
        $movement = StockMovement::query()->create($movementData);
        return $movement->id;
    }

    public function findByInventory($inventoryId)
    {
        $query = StockMovement::query()->where('inventory_id', $inventoryId);
        return $query->orderBy('created_at', 'desc')->get();
    }

    public function getAll(array $filters = [])
    {
        $query = StockMovement::query();

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
