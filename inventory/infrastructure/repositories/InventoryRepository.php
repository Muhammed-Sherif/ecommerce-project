<?php
namespace inventory\infrastructure\repositories;

use inventory\domains\contracts\IInventoryRepository;
use inventory\domains\models\StockMovementType;
use Illuminate\Support\Facades\DB;

class InventoryRepository implements IInventoryRepository
{
    public function create(array $inventoryData)
    {
        return DB::table('inventory')->insertGetId($inventoryData);
    }

    public function update($id, array $inventoryData)
    {
        return DB::table('inventory')
            ->where('id', $id)
            ->update($inventoryData);
    }

    public function findById($id)
    {
        return DB::table('inventory')->where('id', $id)->first();
    }

    public function findByProduct($productId)
    {
        return DB::table('inventory')->where('product_id', $productId)->first();
    }

    public function getAll(array $filters = [])
    {
        $query = DB::table('inventory');

        if (!empty($filters['warehouse_location'])) {
            $query->where('warehouse_location', $filters['warehouse_location']);
        }

        return $query->orderBy('product_id')->get();
    }

    public function reserve($productId, int $quantity)
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $inventory = $this->findByProduct($productId);

            if (!$inventory) {
                throw new \RuntimeException('Inventory not found');
            }

            $available = $inventory->quantity - $inventory->reserved_quantity;
            if ($available < $quantity) {
                return false;
            }

            DB::table('inventory')
                ->where('product_id', $productId)
                ->increment('reserved_quantity', $quantity);

            // Log stock movement
            DB::table('stock_movements')->insert([
                'inventory_id' => $inventory->id,
                'type' => StockMovementType::RESERVED,
                'quantity' => $quantity,
                'reason' => 'Stock reserved for order',
                'created_at' => now(),
            ]);

            return $this->findByProduct($productId);
        });
    }

    public function release($productId, int $quantity)
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $inventory = $this->findByProduct($productId);

            if (!$inventory) {
                throw new \RuntimeException('Inventory not found');
            }

            DB::table('inventory')
                ->where('product_id', $productId)
                ->decrement('reserved_quantity', $quantity);

            // Log stock movement
            DB::table('stock_movements')->insert([
                'inventory_id' => $inventory->id,
                'type' => StockMovementType::RELEASED,
                'quantity' => $quantity,
                'reason' => 'Stock released from reservation',
                'created_at' => now(),
            ]);

            return $this->findByProduct($productId);
        });
    }

    public function adjustStock($productId, int $quantity, string $reason)
    {
        return DB::transaction(function () use ($productId, $quantity, $reason) {
            $inventory = $this->findByProduct($productId);

            if (!$inventory) {
                // Create inventory if it doesn't exist
                $inventoryId = $this->create([
                    'product_id' => $productId,
                    'quantity' => max(0, $quantity),
                    'reserved_quantity' => 0,
                    'warehouse_location' => 'default',
                ]);
                $inventory = $this->findById($inventoryId);
            } else {
                DB::table('inventory')
                    ->where('product_id', $productId)
                    ->increment('quantity', $quantity);
            }

            // Log stock movement
            $movementType = $quantity > 0 ? StockMovementType::IN : StockMovementType::OUT;
            DB::table('stock_movements')->insert([
                'inventory_id' => $inventory->id,
                'type' => $movementType,
                'quantity' => abs($quantity),
                'reason' => $reason,
                'created_at' => now(),
            ]);

            return $this->findByProduct($productId);
        });
    }
}
