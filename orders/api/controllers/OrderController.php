<?php
namespace orders\api\controllers;

use orders\application\commands\CreateOrderHandler;
use orders\application\commands\UpdateOrderStatusHandler;
use orders\application\commands\CancelOrderHandler;
use orders\application\queries\GetOrderHandler;
use orders\application\queries\GetAllOrdersHandler;

class OrderController
{
    private $createOrderHandler;
    private $updateOrderStatusHandler;
    private $cancelOrderHandler;
    private $getOrderHandler;
    private $getAllOrdersHandler;

    public function __construct(
        CreateOrderHandler $createOrderHandler,
        UpdateOrderStatusHandler $updateOrderStatusHandler,
        CancelOrderHandler $cancelOrderHandler,
        GetOrderHandler $getOrderHandler,
        GetAllOrdersHandler $getAllOrdersHandler
    ) {
        $this->createOrderHandler = $createOrderHandler;
        $this->updateOrderStatusHandler = $updateOrderStatusHandler;
        $this->cancelOrderHandler = $cancelOrderHandler;
        $this->getOrderHandler = $getOrderHandler;
        $this->getAllOrdersHandler = $getAllOrdersHandler;
    }

    /**
     * Create a new order
     * POST /orders
     */
    public function create($request)
    {
        try {
            $data = $request->all();
            $result = $this->createOrderHandler->handle($data);

            return response()->json($result, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all orders
     * GET /orders
     */
    public function index($request)
    {
        try {
            $data = $request->all();
            $result = $this->getAllOrdersHandler->handle($data);

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch orders: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a single order by ID
     * GET /orders/{id}
     */
    public function show($request, $id)
    {
        try {
            $result = $this->getOrderHandler->handle(['order_id' => $id]);

            return response()->json($result, 200);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order status
     * PUT /orders/{id}/status
     */
    public function updateStatus($request, $id)
    {
        try {
            $data = $request->all();
            $data['order_id'] = $id;
            
            $result = $this->updateOrderStatusHandler->handle($data);

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
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update order status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel an order
     * POST /orders/{id}/cancel
     */
    public function cancel($request, $id)
    {
        try {
            $result = $this->cancelOrderHandler->handle(['order_id' => $id]);

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
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to cancel order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
