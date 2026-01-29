<?php
namespace inventory\application\commands;

use inventory\domains\contracts\IInventoryRepository;

class ReserveStockHandler
{
    private $repository;
    private $reserveStock;

    public function __construct(IInventoryRepository $repository, ReserveStock $reserveStock)
    {
        $this->repository = $repository;
        $this->reserveStock = $reserveStock;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->reserveStock::execute($data);

        // Reserve stock
        $result = $this->repository->reserve(
            $validatedData['product_id'],
            $validatedData['quantity']
        );

        if (!$result) {
            throw new \RuntimeException('Insufficient stock available');
        }

        return [
            'success' => true,
            'inventory' => $result,
        ];
    }
}
