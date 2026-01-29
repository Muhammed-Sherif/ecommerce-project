<?php
// Define dummy classes for type hints if needed, or rely on existing ones if autoload works.
// We will reuse the autoload logic from verify_script or just include files manually if needed.

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

use payments\infrastructure\PaymentGateways\PaymobGateway;
use payments\application\queries\GetPaymentLinkHandler;
use payments\shared\PaymentApi;

// Mock environment variables for testing
putenv('PAYMOB_API_KEY=test_api_key');
putenv('PAYMOB_INTEGRATION_ID=12345');
putenv('PAYMOB_IFRAME_ID=67890');

try {
    echo "Instantiating PaymobGateway...\n";
    $gateway = new PaymobGateway();
    echo "PaymobGateway instantiated.\n";

    echo "Instantiating GetPaymentLinkHandler...\n";
    $handler = new GetPaymentLinkHandler($gateway);
    echo "GetPaymentLinkHandler instantiated.\n";

    echo "Instantiating PaymentApi...\n";
    $api = new PaymentApi($handler);
    echo "PaymentApi instantiated.\n";

    // We cannot easily test the actual call without hitting external API or mocking curl.
    // For now, we verify the structure.
    
    // Optional: Reflection to verify properties are set
    $reflection = new ReflectionClass($gateway);
    $prop = $reflection->getProperty('apiKey');
    $prop->setAccessible(true);
    if ($prop->getValue($gateway) === 'test_api_key') {
        echo "Environment variables loaded correctly.\n";
    } else {
        echo "Failed to load environment variables.\n";
    }

    echo "Paymob Integration Structure Verified.\n";

} catch (Exception $e) {
    echo "Verification Failed: " . $e->getMessage() . "\n";
}
