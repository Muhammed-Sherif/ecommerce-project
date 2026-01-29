<?php
namespace products\application\commands;

use products\domains\contracts\IProductRepository;
use inventory\domains\contracts\IInventoryRepository;

class CreateProductHandler
{
    private $repository;
    private $createProduct;
    private $inventoryRepository;

    public function __construct(
        IProductRepository $repository,
        CreateProduct $createProduct,
        IInventoryRepository $inventoryRepository
    )
    {
        $this->repository = $repository;
        $this->createProduct = $createProduct;
        $this->inventoryRepository = $inventoryRepository;
    }

    public function handle(array $data)
    {
        $productData = $this->createProduct::execute($data);
        $id = $this->repository->create($productData);
        $created = $this->repository->findById($id);

        $stock = isset($data['stock']) ? (int) $data['stock'] : 0;
        $existingInventory = $this->inventoryRepository->findByProduct($id);
        if ($existingInventory) {
            $this->inventoryRepository->update($existingInventory->id, [
                'quantity' => $stock,
            ]);
        } else {
            $this->inventoryRepository->create([
                'product_id' => $id,
                'quantity' => max(0, $stock),
                'reserved_quantity' => 0,
                'warehouse_location' => 'default',
            ]);
        }

        return [
            'success' => true,
            'product' => $created ?? $productData,
        ];
    }
}
