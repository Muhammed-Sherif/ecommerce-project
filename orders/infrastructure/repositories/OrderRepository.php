<?php
namespace orders\infrastructure\repositories;

use orders\domains\contracts\IOrderRepository;
use Illuminate\Support\Facades\DB;
use shared\EventBus;
class OrderRepository implements IOrderRepository
{
    public function create(array $orderData, array $items)
    {
        return DB::transaction(function () use ($orderData, $items) {
            // Insert order
            $orderId = DB::table('orders')->insertGetId($orderData);

            // Insert order items
            foreach ($items as $item) {
                $item['order_id'] = $orderId;
                DB::table('order_items')->insert($item);
            }
            
            return $orderId;
        });
    }

    public function update($id, array $orderData)
    {
        return DB::table('orders')
            ->where('id', $id)
            ->update($orderData);
    }

    public function findById($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();

        if (!$order) {
            return null;
        }

        // Fetch order items
        $items = DB::table('order_items')
            ->where('order_id', $id)
            ->get();

        // Convert to array and add items
        $orderArray = (array) $order;
        $orderArray['items'] = $items;

        return (object) $orderArray;
    }

    public function findByCustomerId($customerId)
    {
        $orders = DB::table('orders')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch items for each order
        foreach ($orders as $order) {
            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();
            $order->items = $items;
        }

        return $orders;
    }

    public function getAll(array $filters = [])
    {
        $query = DB::table('orders');

        // Apply filters
        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply pagination
        if (!empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
            $page = $filters['page'] ?? 1;
            $offset = ($page - 1) * $perPage;

            $query->limit($perPage)->offset($offset);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        // Fetch items for each order
        foreach ($orders as $order) {
            $items = DB::table('order_items')
                ->where('order_id', $order->id)
                ->get();
            $order->items = $items;
        }

        return $orders;
    }

    public function findByGatewayOrderId($gatewayOrderId)
    {
        $order = DB::table('orders')->where('gateway_order_id', $gatewayOrderId)->first();

        if (!$order) {
            return null;
        }

        return $this->findById($order->id);
    }
}
