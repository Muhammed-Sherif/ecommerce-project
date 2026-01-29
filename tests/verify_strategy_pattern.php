<?php
// Autoloader simulation
spl_autoload_register(function ($class) {
    // Basic PSR-4 mapping simulation
    $prefix = ''; // Base namespace
    $base_dir = __DIR__ . '/../'; // Project root
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        // Fallback for case sensitivity or minor path issues (simplified)
        // echo "Could not find $file\n";
    }
});

use payments\shared\PaymentApi;
use payments\infrastructure\PaymentGateways\PaymentGatewayFactory;

// Mock Environment for Paymob
putenv('PAYMOB_API_KEY=test');
putenv('PAYMOB_INTEGRATION_ID=123');
putenv('PAYMOB_IFRAME_ID=456');

try {
    echo "Testing Payment Strategy Pattern...\n";

    // 1. Test Factory
    echo "1. Testing Factory 'egypt'...\n";
    $strategy = PaymentGatewayFactory::create('egypt');
    if ($strategy instanceof \payments\infrastructure\PaymentGateways\PaymobGateway) {
        echo "   Factory returned PaymobGateway correctly.\n";
    } else {
        echo "   FAILED: Factory returned wrong type.\n";
    }

    // 2. Test PaymentApi
    echo "2. Testing PaymentApi...\n";
    $api = new PaymentApi(); // No constructor args needed anymore
    
    // We expect this to fail with Paymob API error because credentials are fake, 
    // but we want to confirm it reaches the gateway.
    try {
        $api->getPaymentLink('egypt', 100, 'EGP', 'ORD-TEST-1');
    } catch (\Exception $e) {
        echo "   Caught expected exception from Paymob Gateway: " . $e->getMessage() . "\n";
        if (strpos($e->getMessage(), 'Paymob') !== false || strpos($e->getMessage(), 'cURL') !== false) {
             echo "   SUCCESS: PaymentApi successfully delegated to PaymobGateway.\n";
        } else {
             echo "   WARNING: Exception might be unrelated to Paymob.\n";
        }
    }

} catch (\Throwable $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
