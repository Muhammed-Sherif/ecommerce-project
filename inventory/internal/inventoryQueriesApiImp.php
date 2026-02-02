<?php
namespace inventory\internal;

use inventory\shared\IInventoryQueriesGetway;
use inventory\infrastructure\repositories\InventoryRepository;

class InventoryQueriesApiImp implements IInventoryQueriesGetway
{
	private $inventoryRepository;

    public function __construct( InventoryRepository $inventoryRepository)
	{
        $this->inventoryRepository = $inventoryRepository;
    }

	public function getInventoryForProduct($productId)
	{
		if (empty($productId)) {
			throw new \InvalidArgumentException('Invalid arguments provided to getInventoryForProduct');
		}

		$existing = $this->inventoryRepository->findByProduct($productId);
		if ($existing) {
			return $existing;
		}
	}

}
