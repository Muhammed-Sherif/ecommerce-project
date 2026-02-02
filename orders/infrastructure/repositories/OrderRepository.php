<?php
namespace orders\infrastructure\repositories;

use orders\domains\contracts\IOrderRepository;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use shared\EventBus;
use orders\domains\models\OrderStatus;
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
        $query = Order::with('customer')->where('id', $id);
        $user = auth()->user();
        if ($user && ($user->role !== 'admin' || strtolower((string) ($user->status ?? '')) !== 'active')) {
            if ($user->role === 'vendor' && strtolower((string) ($user->status ?? '')) === 'active') {
                $query->where('vendor_id', $user->id);
            } else {
                $query->where('customer_id', $user->id);
            }
        }
        $order = $query->first();

        if (!$order) {
            return null;
        }

        // Fetch order items
        $items = OrderItem::query()->where('order_id', $id)->get();

        $orderArray = $order->toArray();
        $orderArray['items'] = $items;

        return (object) $orderArray;
    }

    public function findByCustomerId($customerId)
    {
        $query = Order::with('customer')->where('customer_id', $customerId);
        $user = auth()->user();
        if ($user && ($user->role !== 'admin' || strtolower((string) ($user->status ?? '')) !== 'active')) {
            if ($user->role === 'vendor' && strtolower((string) ($user->status ?? '')) === 'active') {
                $query->where('vendor_id', $user->id);
            } else {
                $query->where('customer_id', $user->id);
            }
        }
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
        $query = Order::with('customer');

        $user = auth()->user();
        if ($user && ($user->role !== 'admin' || strtolower((string) ($user->status ?? '')) !== 'active')) {
            if ($user->role === 'vendor' && strtolower((string) ($user->status ?? '')) === 'active') {
                $query->where('vendor_id', $user->id);
            } else {
                $query->where('customer_id', $user->id);
            }
        }

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

    public function getCustomersForVendor($vendorId = null, $status = 'paid')
    {
        $query = DB::table('orders')
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'users.id', '=', 'orders.customer_id')
            ->when($status, function ($q) use ($status) {
                $q->where('orders.status', $status);
            })
            ->when($vendorId, function ($q) use ($vendorId) {
                $q->where('order_items.vendor_id', $vendorId);
            })
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.shipping_street',
                'users.shipping_city',
                'users.shipping_state',
                'users.shipping_country',
                'users.shipping_zip_code',
                DB::raw('MAX(orders.created_at) as last_order_at'),
                DB::raw('COUNT(DISTINCT orders.id) as orders_count')
            )
            ->groupBy(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.shipping_street',
                'users.shipping_city',
                'users.shipping_state',
                'users.shipping_country',
                'users.shipping_zip_code'
            )
            ->orderByDesc('last_order_at');

        return $query->get();
    }
    public function checkForDeliveredOrderForUser( $customerId, $productId)
    {
      return Order::where('customer_id', $customerId)
                ->where('status', OrderStatus::DELIVERED)
                ->whereExists(function ($query) use ($productId) {
                    $query->selectRaw(1)
                        ->from('order_items')
                        ->whereColumn('order_items.order_id', 'orders.id')
                        ->where('order_items.product_id', $productId);
                })
                ->exists();
    }
}
