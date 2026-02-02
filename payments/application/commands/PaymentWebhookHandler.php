<?php
namespace payments\application\commands;

use payments\domains\models\PaymentGatewayContext;
use payments\infrastructure\PaymentGateways\PaymentGatewayFactory;
use orders\application\commands\UpdateOrderStatusHandler;
use orders\domains\models\OrderStatus;
use accounts\domains\contracts\Iuser;
use inventory\domains\contracts\IInventoryRepository;
use Illuminate\Support\Facades\Log;
use orders\domains\contracts\IOrderRepository;
class PaymentWebhookHandler
{
    private $orderStatusHandler;
    private $orderRepository;
    private $userRepository;
    private $inventoryRepository;

    public function __construct(
        UpdateOrderStatusHandler $orderStatusHandler,
        IOrderRepository $orderRepository,
        Iuser $userRepository,
        IInventoryRepository $inventoryRepository
    )
    {
        $this->orderStatusHandler = $orderStatusHandler;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->inventoryRepository = $inventoryRepository;
    }

    public function handle(string $gatewayIdentifier, array $data)
    {
        Log::info("PaymentWebhookHandler::handle", ['gateway' => $gatewayIdentifier, 'data' => $data]);

        $gateway = PaymentGatewayFactory::create($gatewayIdentifier);
        $context = new PaymentGatewayContext($gateway);
        
        $result = $context->handleWebhook($data);
        Log::info("Webhook parsed result", ['result' => $result]);

        $type = strtoupper($result['type'] ?? 'TRANSACTION');
        $orderId = $result['order_id'] ?? null;

        if ($type === 'TRANSACTION' && $orderId && $result['status'] === 'success') {
            Log::info("Payment successful, finding local order", ['gateway_order_id' => $result['order_id']]);
            
            $order = $this->orderRepository->findByGatewayOrderId($result['order_id']);
            
            if (!$order) {
                Log::error("Order not found for gateway_order_id", ['gateway_order_id' => $result['order_id']]);
                return ['success' => false, 'message' => 'Order not found'];
            }

            try {
                $this->orderStatusHandler->handle([
                    'order_id' => $order->id,
                    'status' => OrderStatus::PAID
                ]);
                $this->decrementStockForOrder($order);
                return ['success' => true, 'message' => 'Order status updated to paid'];
            } catch (\Exception $e) {
                Log::error("Failed to update order status", ['error' => $e->getMessage()]);
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }

        if ($type === 'TOKEN' && $orderId && ($result['token'] ?? null)) {
            Log::info("Token received, finding local order", ['gateway_order_id' => $orderId]);
            
            $order = $this->orderRepository->findByGatewayOrderId($orderId);
            
            if (!$order) {
                Log::error("Order not found for gateway_order_id", ['gateway_order_id' => $orderId]);
                return ['success' => false, 'message' => 'Order not found'];
            }

            try {
                $this->userRepository->update($order->customer_id, [
                    'payment_token' => $result['token']
                ]);
                return ['success' => true, 'message' => 'Payment token stored'];
            } catch (\Exception $e) {
                Log::error("Failed to store payment token", ['error' => $e->getMessage()]);
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }

        return ['success' => false, 'message' => 'Payment failed or unsupported webhook type', 'result' => $result];
    }

    private function decrementStockForOrder($order): void
    {
        $items = $order->items ?? [];
        if (!is_iterable($items)) {
            return;
        }

        foreach ($items as $item) {
            $itemArray = (array) $item;
            $productId = $itemArray['product_id'] ?? null;
            $quantity = isset($itemArray['quantity']) ? (int) $itemArray['quantity'] : 0;

            if (!$productId || $quantity <= 0) {
                continue;
            }

            try {
                $this->inventoryRepository->adjustStock($productId,$quantity );
            } catch (\Exception $e) {
                Log::error('Failed to decrement stock after payment', [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'error' => $e->getMessage(),
                ]);
                throw $e;
            }
        }
    }
}
