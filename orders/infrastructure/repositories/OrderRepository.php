<?php
namespace orders\infrastructure\repositories;

use orders\domains\contracts\IOrderRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use shared\EventBus;
class OrderRepository implements IOrderRepository
{
    public function create(array $orderData, array $items)
    {
        return DB::transaction(function () use ($orderData, $items) {
            // Insert order
            $order = Order::query()->create($orderData);
            $orderId = $order->id;

            // Insert order items
            foreach ($items as $item) {
                $item['order_id'] = $orderId;
                OrderItem::query()->create($item);
            }
            
            return $orderId;
        });
    }

    public function update($id, array $orderData)
    {
        $query = Order::query()->where('id', $id);
        return $query->update($orderData);
    }

    public function findById($id)
    {
        $query = Order::query()->where('id', $id);
        $order = $query->first();

        if (!$order) {
            return null;
        }

        // Fetch order items
        $items = OrderItem::query()->where('order_id', $id)->get();

        // Convert to array and add items
        $orderArray = (array) $order;
        $orderArray['items'] = $items;

        return (object) $orderArray;
    }

    public function findByCustomerId($customerId)
    {
        $query = Order::query()->where('customer_id', $customerId);
        $orders = $query->orderBy('created_at', 'desc')->get();

        // Fetch items for each order
        foreach ($orders as $order) {
            $items = OrderItem::query()->where('order_id', $order->id)->get();
            $order->items = $items;
        }

        return $orders;
    }

    public function getAll(array $filters = [])
    {
        $query = Order::query();

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
            $items = OrderItem::query()->where('order_id', $order->id)->get();
            $order->items = $items;
        }

        return $orders;
    }

    public function findByGatewayOrderId($gatewayOrderId)
    {
        $query = Order::query()->where('gateway_order_id', $gatewayOrderId);
        $order = $query->first();

        if (!$order) {
            return null;
        }

        return $this->findById($order->id);
    }
}
