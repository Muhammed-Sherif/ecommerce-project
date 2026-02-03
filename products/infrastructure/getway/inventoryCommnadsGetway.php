<?php
namespace products\infrastructure\getway;

use products\domains\contracts\IInventoryCommandsGetway ;
use products\infrastructure\dtos\InventoryCommandResultDto;
use inventory\shared\IInventoryCommandsGetway as InventorySharedGetway;

class InventoryCommnadsGetway implements IInventoryCommandsGetway 
{
	/** @var InventorySharedGetway */
	private $inventoryApi;

	public function __construct(InventorySharedGetway $inventoryApi)
	{
		$this->inventoryApi = $inventoryApi;
	}

	public function updateInventoryForProduct($productId, array $data)
	{
		$result = $this->inventoryApi->updateInventoryForProduct($productId, $data);
		return InventoryCommandResultDto::fromResult($result);
	}

	public function createInventoryForProduct($productId, array $data)
	{
		$result = $this->inventoryApi->createInventoryForProduct($productId, $data);
		return InventoryCommandResultDto::fromResult($result);
	}
}
