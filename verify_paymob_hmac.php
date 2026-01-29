<?php
require 'framwork/internal/vendor/autoload.php';
$app = require_once 'framwork/internal/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// We need to mock the request() helper for the test
use Illuminate\Http\Request;
use payments\infrastructure\PaymentGateways\PaymobGateway;

echo "Verifying Paymob HMAC Logic\n";

// Sample Secret from user docs (actually I'll just use a test one)
$testSecret = 'test_secret';

// 1. TRANSACTION TEST
$txData = [
    "type" => "TRANSACTION",
    "obj" => [
        "id" => 192036465,
        "pending" => false,
        "amount_cents" => 100000,
        "success" => true,
        "is_auth" => false,
        "is_capture" => false,
        "is_standalone_payment" => true,
        "is_voided" => false,
        "is_refunded" => false,
        "is_3d_secure" => true,
        "integration_id" => 4097558,
        "profile_id" => 164295,
        "has_parent_transaction" => false,
        "order" => [
            "id" => 217503754
        ],
        "created_at" => "2024-06-13T11:33:44.592345",
        "currency" => "EGP",
        "source_data" => [
            "pan" => "2346",
            "type" => "card",
            "sub_type" => "MasterCard"
        ],
        "owner" => 302852
    ]
];

// Expected String: 1000002024-06-13T11:33:44.592345EGPfalsefalse1920364654097558truefalsefalsefalsetruefalse217503754302852false2346MasterCardcardtrue
$expectedString = "1000002024-06-13T11:33:44.592345EGPfalsefalse1920364654097558truefalsefalsefalsetruefalse217503754302852false2346MasterCardcardtrue";
$expectedHmac = hash_hmac('sha512', $expectedString, $testSecret);

echo "Expected HMAC: $expectedHmac\n";

// Create gateway instance
$gateway = new PaymobGateway();

// Using Reflection to test private method validateHMAC
$reflection = new ReflectionClass($gateway);
$method = $reflection->getMethod('validateHMAC');
$method->setAccessible(true);

// Add a property to capture the string if possible, or just re-implement here for debug
function getConcatenatedString($data) {
    $obj = $data['obj'] ?? [];
    $keys = [
        'amount_cents', 'created_at', 'currency', 'error_occured', 
        'has_parent_transaction', 'id', 'integration_id', 'is_3d_secure', 
        'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment', 
        'is_voided', 'order.id', 'owner', 'pending', 'source_data.pan', 
        'source_data.sub_type', 'source_data.type', 'success'
    ];
    $str = '';
    foreach ($keys as $key) {
        $val = '';
        if ($key === 'order.id') {
            $val = $obj['order']['id'] ?? '';
        } elseif (strpos($key, 'source_data.') === 0) {
            $subKey = substr($key, 12);
            $val = $obj['source_data'][$subKey] ?? '';
        } elseif ($key === 'id') {
            $val = $obj['id'] ?? '';
        } else {
            $val = $obj[$key] ?? '';
        }

        if (is_bool($val)) {
            $val = $val ? 'true' : 'false';
        }
        $str .= $val;
    }
    return $str;
}

$actualString = getConcatenatedString($txData);

if ($actualString === $expectedString) {
    echo "Strings match!\n";
} else {
    echo "Strings DO NOT match.\n";
    $len1 = strlen($actualString);
    $len2 = strlen($expectedString);
    echo "Actual Length: $len1, Expected Length: $len2\n";
    
    for ($i = 0; $i < min($len1, $len2); $i++) {
        if ($actualString[$i] !== $expectedString[$i]) {
            echo "Mismatch at index $i: Actual='{$actualString[$i]}', Expected='{$expectedString[$i]}'\n";
            echo "Context: " . substr($actualString, max(0, $i-10), 20) . "\n";
            break;
        }
    }
}

$isValid = $method->invokeArgs($gateway, [$txData, $expectedHmac, $testSecret, 'TRANSACTION']);

if ($isValid) {
    echo "TRANSACTION HMAC VALIDATION SUCCESSFUL!\n";
} else {
    echo "TRANSACTION HMAC VALIDATION FAILED.\n";
}

// 2. TOKEN TEST
$tokenData = [
    "type" => "TOKEN",
    "obj" => [
        "card_subtype" => "MasterCard",
        "created_at" => "2024-11-13T12:32:23.859982",
        "email" => "test@test.com",
        "id" => 8555026,
        "masked_pan" => "xxxx-xxxx-xxxx-2346",
        "merchant_id" => 246628,
        "order_id" => 264064419,
        "token" => "e98aceb96f5a370ddf46460db9d555f88bf12448f80e1839b39f78ab"
    ]
];

// Expected String: MasterCard2024-11-13T12:32:23.859982test@test.com8555026xxxx-xxxx-xxxx-2346246628264064419e98aceb96f5a370ddf46460db9d555f88bf12448f80e1839b39f78ab
$expectedTokenString = "MasterCard2024-11-13T12:32:23.859982test@test.com8555026xxxx-xxxx-xxxx-2346246628264064419e98aceb96f5a370ddf46460db9d555f88bf12448f80e1839b39f78ab";
$expectedTokenHmac = hash_hmac('sha512', $expectedTokenString, $testSecret);

$isTokenValid = $method->invokeArgs($gateway, [$tokenData, $expectedTokenHmac, $testSecret, 'TOKEN']);

if ($isTokenValid) {
    echo "TOKEN HMAC VALIDATION SUCCESSFUL!\n";
} else {
    echo "TOKEN HMAC VALIDATION FAILED.\n";
}
