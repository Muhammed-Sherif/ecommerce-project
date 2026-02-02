<?php
namespace cart\infrastructure\getway;

use cart\domains\contracts\IInventoryQueriesGetway;
use cart\infrastructure\dtos\InventoryQueriesResultDto;
use inventory\shared\IInventoryQueriesGetway as InventorySharedGetway;

class InventoryQueriesGetway implements IInventoryQueriesGetway
{
	/** @var InventorySharedGetway */
	private $inventoryApi;

	public function __construct(InventorySharedGetway $inventoryApi)
	{
		$this->inventoryApi = $inventoryApi;
	}

	public function getInventoryForProduct($productId)
	{
		$result = $this->inventoryApi->getInventoryForProduct($productId);
		return InventoryQueriesResultDto::fromResult($result);
	}

}
