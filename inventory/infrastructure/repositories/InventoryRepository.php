<?php
namespace inventory\infrastructure\repositories;

use inventory\domains\contracts\IInventoryRepository;
use inventory\domains\models\StockMovementType;
use App\Models\Inventory;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryRepository implements IInventoryRepository
{
    public function create(array $inventoryData)
    {
        $inventory = Inventory::query()->create($inventoryData);
        return $inventory->id;
    }

    public function update($id, array $inventoryData)
    {
        $query = Inventory::query()->where('id', $id);
        return $query->update($inventoryData);
    }

    public function findById($id)
    {
        $query = Inventory::query()->where('id', $id);
        return $query->first();
    }

    public function findByProduct($productId)
    {
        $query = Inventory::query()->where('product_id', $productId);
        return $query->first();
    }

    public function getAll(array $filters = [])
    {
        $query = Inventory::query();

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

            Inventory::query()
                ->where('product_id', $productId)
                ->increment('reserved_quantity', $quantity);

            // Log stock movement
            StockMovement::query()->create([
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

            Inventory::query()
                ->where('product_id', $productId)
                ->decrement('reserved_quantity', $quantity);

            // Log stock movement
            StockMovement::query()->create([
                'inventory_id' => $inventory->id,
                'type' => StockMovementType::RELEASED,
                'quantity' => $quantity,
                'reason' => 'Stock released from reservation',
                'created_at' => now(),
            ]);

            return $this->findByProduct($productId);
        });
    }

    public function adjustStock($productId, int $quantity)
    {
        return DB::transaction(function () use ($productId, $quantity) {
            $inventory = $this->findByProduct($productId);
            if ($inventory) {
                Inventory::query()
                    ->where('product_id', $productId)
                    ->increment('quantity', $quantity);
                return $this->findByProduct($productId);
            } else {
                throw new \RuntimeException('Inventory not found');
            }
        });
    }

    public function deleteByProduct($productId)
    {
        $inventory = $this->findByProduct($productId);
        if (!$inventory) {
            return false;
        }

        StockMovement::query()->where('inventory_id', $inventory->id)->delete();
        return Inventory::query()->where('product_id', $productId)->delete();
    }
}
