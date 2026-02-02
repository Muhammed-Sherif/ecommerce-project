<?php
namespace inventory\internal;

use inventory\shared\IInventoryCommandsGetway;
use inventory\infrastructure\repositories\InventoryRepository;

class InventoryCommandsApiImp implements IInventoryCommandsGetway
{
	private $inventoryRepository;

    public function __construct( InventoryRepository $inventoryRepository)
	{
        $this->inventoryRepository = $inventoryRepository;
    }

	public function updateInventoryForProduct($productId, array $data)
	{
		if (empty($productId) || !is_array($data)) {
			throw new \InvalidArgumentException('Invalid arguments provided to updateInventoryForProduct');
		}

		$inventoryData = [];

		if (array_key_exists('quantity', $data)) {
			$inventoryData['quantity'] = max(0, (int) $data['quantity']);
		}

		if (empty($inventoryData)) {
			return false;
		}

		$existing = $this->inventoryRepository->findByProduct($productId);
		if ($existing) {
			return $this->inventoryRepository->update($existing->id, $inventoryData);
		}
	}

	public function createInventoryForProduct($productId, array $inventoryData)
	{
		$quantity = 0;
		if (array_key_exists('quantity', $inventoryData)) {
			$quantity = max(0, (int) $inventoryData['quantity']);
		}

		return $this->inventoryRepository->create([
			'product_id' => $productId,
			'quantity' => $quantity,
			'reserved_quantity' => 0,
			'warehouse_location' => 'default',
		]);
	}
}
