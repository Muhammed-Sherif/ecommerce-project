<?php
namespace orders\application\commands;

use orders\domains\contracts\IOrderRepository;
use orders\domains\models\OrderStatus;

class UpdateOrderStatusHandler
{
    private $repository;
    private $updateOrderStatus;

    public function __construct(IOrderRepository $repository, UpdateOrderStatus $updateOrderStatus)
    {
        $this->repository = $repository;
        $this->updateOrderStatus = $updateOrderStatus;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->updateOrderStatus::execute($data);

        // Get current order
        $order = $this->repository->findById($validatedData['order_id']);

        if (!$order) {
            throw new \RuntimeException('Order not found');
        }

        $currentStatus = $order->status ?? $order['status'];
        $newStatus = $validatedData['status'];

        // Check if transition is valid
        if (!OrderStatus::canTransition($currentStatus, $newStatus)) {
            throw new \InvalidArgumentException(
                "Cannot transition from {$currentStatus} to {$newStatus}"
            );
        }

        // Update order status
        $this->repository->update($validatedData['order_id'], [
            'status' => $newStatus,
        ]);

        // Fetch updated order
        $updatedOrder = $this->repository->findById($validatedData['order_id']);

        return [
            'success' => true,
            'order' => $updatedOrder,
        ];
    }
}
