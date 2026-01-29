<?php
// Define dummy classes for type hints to avoid loading broken existing code
namespace cart\application\queries {
    class GetCartHandler {}
}

namespace cart\application\commands {
    class ClearCartHandler {}
}

namespace {
    // Autoloader for other classes we want to test
    spl_autoload_register(function ($class) {
        $prefix = '';
        $base_dir = __DIR__ . '/../';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });

    use orders\application\commands\OrderCheckoutHandler;
    use orders\application\commands\CreateOrderHandler;
    use orders\domains\contracts\IOrderRepository;
    use cart\domains\contracts\ICartApi; // Correct Interface
    use cart\application\queries\GetCartHandler;
    use cart\application\commands\ClearCartHandler;
    use payments\shared\PaymentApi;
    use payments\application\queries\GetPaymentLinkHandler;
    use payments\infrastructure\PaymentGateways\MockPaymentGateway;

    // Mock dependencies
    class MockCartApi implements ICartApi {
        public function getCart($userId, GetCartHandler $handler = null) {
            return (object)[
                'items' => [
                    ['id' => 1, 'price' => 50, 'quantity' => 2]
                ],
                'total_amount' => 100
            ];
        }
        public function clearCart($userId, ClearCartHandler $handler = null) {
            echo "Cart cleared for user $userId\n";
        }
    }

    class MockCreateOrderHandler extends CreateOrderHandler {
        public function __construct(){} 
        public function handle(array $cart, $user) {
            echo "Order created\n";
            return [
                'success' => true,
                'order' => (object)['id' => 'ORD-12345', 'total_amount' => 100.00]
            ];
        }
    }

    class MockOrderRepository implements IOrderRepository {
        public function create(array $orderData, array $items) {}
        public function update($id, array $orderData) {
            echo "Order updated: $id\n";
        }
        public function findById($id) {}
        public function findByCustomerId($customerId) {}
        public function getAll(array $filters = []) {}
    }

    // Setup
    $mockCartApi = new MockCartApi();
    $mockCreateOrderHandler = new MockCreateOrderHandler();
    $mockPaymentGateway = new MockPaymentGateway();
    // Verify GetPaymentLinkHandler exists and works
    $paymentHandler = new GetPaymentLinkHandler($mockPaymentGateway);
    $paymentApi = new PaymentApi($paymentHandler);

    $mockOrderRepository = new MockOrderRepository();
    $handler = new OrderCheckoutHandler($mockCreateOrderHandler, $mockCartApi, $paymentApi, $mockOrderRepository);

    // Execute
    $user = (object)[
        'id' => 1,
        'name' => 'John Doe',
        'shipping_street' => '123 Main St',
        'shipping_city' => 'Cairo',
        'shipping_state' => 'Cairo',
        'shipping_country' => 'EG',
        'shipping_zip_code' => '11511'
    ];
    echo "Running Checkout...\n";
    $result = $handler->handle($user);

    echo "Result Payment Link: " . ($result['payment_url'] ?? '') . "\n";
}
