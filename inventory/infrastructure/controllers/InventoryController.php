<?php
namespace inventory\infrastructure\controllers;

use inventory\application\commands\AdjustStockHandler;
use inventory\application\commands\ReserveStockHandler;
use inventory\application\queries\GetInventoryHandler;

class InventoryController
{
    private $adjustStockHandler;
    private $reserveStockHandler;
    private $getInventoryHandler;

    public function __construct(
        AdjustStockHandler $adjustStockHandler,
        ReserveStockHandler $reserveStockHandler,
        GetInventoryHandler $getInventoryHandler
    ) {
        $this->adjustStockHandler = $adjustStockHandler;
        $this->reserveStockHandler = $reserveStockHandler;
        $this->getInventoryHandler = $getInventoryHandler;
    }

    /**
     * Get inventory for a product
     * GET /inventory/product/{productId}
     */
    public function getByProduct($request, $productId)
    {
        try {
            $result = $this->getInventoryHandler->handle(['product_id' => $productId]);
            return response()->json($result, 200);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch inventory: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Adjust stock
     * POST /inventory/adjust
     */
    public function adjust($request)
    {
        try {
            $data = $request->all();
            $result = $this->adjustStockHandler->handle($data);
            return response()->json($result, 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to adjust stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reserve stock
     * POST /inventory/reserve
     */
    public function reserve($request)
    {
        try {
            $data = $request->all();
            $result = $this->reserveStockHandler->handle($data);
            return response()->json($result, 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to reserve stock: ' . $e->getMessage(),
            ], 500);
        }
    }
}
