<?php
namespace orders\application\commands;

use orders\domains\contracts\IOrderRepository;
use orders\domains\models\OrderStatus;
class CreateOrderHandler
{
    private $repository;
    private $createOrder;

    public function __construct(IOrderRepository $repository, CreateOrder $createOrder)
    {
        $this->repository = $repository;
        $this->createOrder = $createOrder;
    }

    public function handle( array $cartItems , $user)
    {
     // Prepare order data
        $orderData = [
            'order_number' => $this->generateOrderNumber(),
            'status' => OrderStatus::PENDING,
            'customer_id' => $user->id,
            'shipping_street' => $user->shipping_street,
            'shipping_city' => $user->shipping_city,
            'shipping_state' => $user->shipping_state,
            'shipping_country' => $user->shipping_country,
            'shipping_zip_code' => $user->shipping_zip_code,
            'phone' => $user->phone,
        ];

        // Map items for database insertion
        $dbItems = array_map(function($item) {
            $itemArray = (array)$item;
            return [
                'product_id' => $itemArray['product_id'],
                'product_name' => $itemArray['name'],
                'quantity' => $itemArray['quantity'],
                'unit_price' => $itemArray['price'],
                'total_price' => $itemArray['price'] * $itemArray['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $cartItems);

        // Calculate total amount
        $orderData['total_amount'] = array_reduce($dbItems, function($carry, $item) {
            return $carry + $item['total_price'];
        }, 0);

        // Validate and prepare order data (Domain logic)
        $domainOrderData = $orderData;
        $domainOrderData['items'] = $dbItems;
        $this->createOrder::execute($domainOrderData);

        // Create order with items in database
        $orderId = $this->repository->create($orderData, $dbItems);

        // Fetch created order
        $createdOrder = $this->repository->findById($orderId);
        
        return [
            'success' => true,
            'order' => $createdOrder,
        ];
    }
    private function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
    }
}
