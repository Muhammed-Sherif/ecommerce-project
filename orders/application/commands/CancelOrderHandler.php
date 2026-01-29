<?php
namespace orders\application\commands;

use orders\domains\contracts\IOrderRepository;
use orders\domains\models\OrderStatus;
use orders\domains\models\Order;

class CancelOrderHandler
{
    private $repository;
    private $cancelOrder;

    public function __construct(IOrderRepository $repository, CancelOrder $cancelOrder)
    {
        $this->repository = $repository;
        $this->cancelOrder = $cancelOrder;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->cancelOrder::execute($data);

        // Get order
        $orderData = $this->repository->findById($validatedData['order_id']);

        if (!$orderData) {
            throw new \RuntimeException('Order not found');
        }

        // Create Order object to check business rules
        $order = Order::fromArray((array) $orderData);

        // Check if order can be cancelled
        if (!$order->canBeCancelled()) {
            throw new \InvalidArgumentException(
                "Order with status '{$order->status}' cannot be cancelled"
            );
        }

        // Update order status to cancelled
        $this->repository->update($validatedData['order_id'], [
            'status' => OrderStatus::CANCELLED,
        ]);

        // Fetch updated order
        $updatedOrder = $this->repository->findById($validatedData['order_id']);

        return [
            'success' => true,
            'order' => $updatedOrder,
        ];
    }
}
