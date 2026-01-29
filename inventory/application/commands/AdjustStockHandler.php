<?php
namespace inventory\application\commands;

use inventory\domains\contracts\IInventoryRepository;
use inventory\domains\contracts\IStockMovementRepository;
use inventory\domains\models\StockMovementType;

class AdjustStockHandler
{
    private $inventoryRepository;
    private $stockMovementRepository;
    private $adjustStock;

    public function __construct(
        IInventoryRepository $inventoryRepository,
        IStockMovementRepository $stockMovementRepository,
        AdjustStock $adjustStock
    ) {
        $this->inventoryRepository = $inventoryRepository;
        $this->stockMovementRepository = $stockMovementRepository;
        $this->adjustStock = $adjustStock;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->adjustStock::execute($data);

        // Adjust stock
        $result = $this->inventoryRepository->adjustStock(
            $validatedData['product_id'],
            $validatedData['quantity'],
            $validatedData['reason']
        );

        return [
            'success' => true,
            'inventory' => $result,
        ];
    }
}
