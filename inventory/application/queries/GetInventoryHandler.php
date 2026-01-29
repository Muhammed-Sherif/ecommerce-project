<?php
namespace inventory\application\queries;

use inventory\domains\contracts\IInventoryRepository;

class GetInventoryHandler
{
    private $repository;
    private $getInventory;

    public function __construct(IInventoryRepository $repository, GetInventory $getInventory)
    {
        $this->repository = $repository;
        $this->getInventory = $getInventory;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->getInventory::execute($data);

        // Fetch inventory
        $inventory = $this->repository->findByProduct($validatedData['product_id']);

        if (!$inventory) {
            throw new \RuntimeException('Inventory not found for this product');
        }

        return [
            'success' => true,
            'inventory' => $inventory,
        ];
    }
}
