<?php
namespace inventory\infrastructure\repositories;

use inventory\domains\contracts\IStockMovementRepository;
use Illuminate\Support\Facades\DB;

class StockMovementRepository implements IStockMovementRepository
{
    public function create(array $movementData)
    {
        return DB::table('stock_movements')->insertGetId($movementData);
    }

    public function findByInventory($inventoryId)
    {
        return DB::table('stock_movements')
            ->where('inventory_id', $inventoryId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAll(array $filters = [])
    {
        $query = DB::table('stock_movements');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
