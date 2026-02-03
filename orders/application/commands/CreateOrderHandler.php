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

    public function handle( array $cartItems , $user , $couponCode = null , $discountedAmount = 0 , $originalTotalAmount = 0)
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

        $vendorIds = [];

        // Map items for database insertion
        $dbItems = array_map(function($item) use (&$vendorIds) {
            $itemArray = (array)$item;
            $itemQuantity = isset($itemArray['quantity']) ? (int)$itemArray['quantity'] : 1;
            $unitPrice = isset($itemArray['price'])
                ? (float)$itemArray['price']
                : (isset($itemArray['unit_price'])
                    ? (float)$itemArray['unit_price']
                    : (isset($itemArray['product_price']) ? (float)$itemArray['product_price'] : 0));

            if ($unitPrice <= 0 && isset($itemArray['total_price'])) {
                $total = (float)$itemArray['total_price'];
                $unitPrice = $itemQuantity > 0 ? $total / $itemQuantity : 0;
            }
            $sellerId = $itemArray['seller_id'] ?? null;
            if ($sellerId !== null) {
                $sellerIds[$sellerId] = true;
            }
            return [
                'product_id' => $itemArray['product_id'],
                'product_name' => $itemArray['name'],
                'quantity' => $itemQuantity,
                'unit_price' => $unitPrice,
                'total_price' => $unitPrice * $itemQuantity,
                'seller_id' => $sellerId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $cartItems);

        if (count($sellerIds) === 1) {
            $orderData['seller_id'] = array_key_first($sellerIds);
        }

        $orderData['total_amount'] = $originalTotalAmount - $discountedAmount;
        $orderData['discount_amount'] = $discountedAmount;
        $orderData['coupon_code'] = $couponCode;
        $orderData['items'] = $dbItems;
        $this->createOrder::execute($orderData);

        $orderDataForPersist = $orderData;
        unset($orderDataForPersist['items']);

        // Create order with items in database
        $orderId = $this->repository->create($orderDataForPersist, $dbItems);

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
