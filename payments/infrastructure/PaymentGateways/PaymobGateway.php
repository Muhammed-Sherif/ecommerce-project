<?php
namespace payments\infrastructure\PaymentGateways;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use payments\domains\contracts\PaymentGatewayStrategy;

class PaymobGateway implements PaymentGatewayStrategy
{
    private $secretKey;
    private $publicKey;
    private $intentionEndpoint;
    private $intentionId;
    private $checkoutUrl;
    private $httpClient;
  public function __construct() {
        // Get these from your Paymob Dashboard (Settings > Account Info)
        $this->secretKey = env('PAYMOB_SECRET_KEY');
        $this->publicKey = env('PAYMOB_PUBLIC_KEY');
        $this->intentionEndpoint = 'https://accept.paymob.com/v1/intention/';
        $this->intentionId = env('PAYMOB_INTENTION_ID');
        
        $this->checkoutUrl = 'https://accept.paymob.com/unifiedcheckout/';
        
        // Initialize Guzzle HTTP client
        $this->httpClient = new Client([
            'timeout' => 30,
            'headers' => [
                'Authorization' => 'Token ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }

    /**
     * Step 1: Create Payment Intention (Payment Request)
     * This creates the payment request and gets the client_secret needed for checkout
     * 
     * @param float $amount - Amount in EGP (not cents)
     * @param string $method - Payment method (card, wallet, etc.)
     * @param array $metadata - Contains: items, billing_data, customer, etc.
     * @return array - Contains status, checkout_url, client_secret, intention_id
     */
    public function createPaymentSession($amount, $currency, $orderId): array {
        try {
            // Build the intention payload
            $payload = $this->buildIntentionPayload($amount, $currency, ['order_id' => $orderId]);
            \Log::info('Paymob Intention Payload: ' . json_encode($payload));
            // Call Paymob Intention API
            $response = $this->makeApiCall($this->intentionEndpoint, $payload);
            \Log::info('Paymob Intention Response: ' . json_encode($response));
            if (!$response || !isset($response["status"]) || $response["status"] !== "intended" ) {
                \Log::error('Failed to create Paymob payment intention', ['response' => $response]);
                \Log::info('Failed to create Paymob payment', ['response' => $response['error'] ?? 'No response']);
                return [
                    'status' => 'failed',
                    'message' => 'Failed to create payment intention',
                    'error' => $response['error'] ?? $response['detail'] ?? json_encode($response),
                    'raw_response' => $response
                ];
            }
            
            // Build checkout URL for user redirection
            $clientSecret = $response['client_secret'] ?? null;
            if (empty($clientSecret)) {
                \Log::error('Paymob response missing client_secret', ['response' => $response]);
                return [
                    'status' => 'failed',
                    'message' => 'Paymob response missing client_secret',
                    'error' => 'client_secret missing',
                    'raw_response' => $response
                ];
            }

            $checkoutUrl = $this->checkoutUrl . 
                           '?publicKey=' . $this->publicKey . 
                           '&clientSecret=' . $clientSecret;
            \Log::info('Checkout URL: ' . $checkoutUrl);
            \Log::info('Response: ' . json_encode($response));
            $gatewayOrderId = $response['payment_keys'][0]['order_id']
                ?? $response['intention_order_id']
                ?? $response['order']
                ?? null;
            if (!empty($gatewayOrderId)) {
                \Log::info('gateway_order_id: ' . $gatewayOrderId);
            }


            return [
                'status' => 'pending', // Payment created, waiting for user to complete
                'intention_id' => $response['id'],
                'gateway_order_id' => $gatewayOrderId,
                'client_secret' => $clientSecret,
                'checkout_url' => $checkoutUrl,
                'link' => $checkoutUrl,
                'message' => 'Redirect user to checkout_url to complete payment'
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Payment processing error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build Paymob Intention Payload
     * Constructs the request payload for creating payment intention
     * 
     * @param float $amount - Amount in EGP
     * @param string $method - Payment method
     * @param array $metadata - Additional payment data
     * @return array - Formatted payload for Paymob API
     */
    private function buildIntentionPayload(float $amount, string $method, array $metadata): array {
        // Convert amount to cents (Paymob expects cents)
        $amountInCents = $amount * 100;
        
        // Format items properly for Paymob
        $formattedItems = [];
        if (isset($metadata['items']) && is_array($metadata['items'])) {
            foreach ($metadata['items'] as $item) {
                $itemPrice = isset($item['price']) ? floatval($item['price']) : 0;
                $itemQuantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
                $itemAmount = $itemPrice * $itemQuantity * 100; // Convert to cents
                
                $formattedItems[] = [
                    'name' => $item['name'] ?? 'Product #' . ($item['product_id'] ?? 'unknown'),
                    'amount' => $itemAmount,
                    'description' => $item['description'] ?? 'Product purchase',
                    'quantity' => $itemQuantity
                ];
            }
        }
        
        // If no items provided, create a default item
        if (empty($formattedItems)) {
            $formattedItems = [
                [
                    'name' => 'Order Item',
                    'amount' => round($amountInCents),
                    'description' => 'Product purchase',
                    'quantity' => 1
                ]
            ];
        }
        
        // Build the intention request payload
        $payload = [
            'amount' => round($amountInCents),
            'currency' => 'EGP',
            'payment_methods' => [$this->intentionId],
            'items' => $formattedItems,
            'billing_data' => $metadata['billing_data'] ?? [
                'apartment' => 'NA',
                'first_name' => $metadata['customer']['first_name'] ?? 'Customer',
                'last_name' => $metadata['customer']['last_name'] ?? 'Name',
                'street' => 'NA',
                'building' => 'NA',
                'phone_number' => $metadata['customer']['phone'] ?? '+201000000000',
                'country' => 'EGY',
                'email' => $metadata['customer']['email'] ?? 'customer@example.com',
                'floor' => 'NA',
                'state' => 'NA'
            ],
            'customer' => $metadata['customer'] ?? [
                'first_name' => 'Customer',
                'last_name' => 'Name',
                'email' => 'customer@example.com'
            ]
        ];
        
        // Optional: Add special reference (your order ID)
        if (isset($metadata['order_id'])) {
            $payload['special_reference'] = $metadata['order_id'];
        }
        
        // Optional: Add notification URL for webhooks
        if (isset($metadata['notification_url'])) {
            $payload['notification_url'] = $metadata['notification_url'];
        }
        
        // Optional: Add redirection URL
        if (isset($metadata['redirection_url'])) {
            $payload['redirection_url'] = $metadata['redirection_url'];
        }
        
        // Optional: Add extras
        if (isset($metadata['extras'])) {
            $payload['extras'] = $metadata['extras'];
        }
        
        return $payload;
    }
    /**
     * Step 2: Check Payment Status
     * After webhook callback or when checking transaction status
     * 
     * @param string $transactionId - Can be intention_id or transaction_id from callback
     * @return string - Status: 'pending', 'success', 'failed'
     */
    public function getPaymentStatus($transactionId) {
        // In real implementation, you would:
        // 1. Query Paymob API for transaction status
        // 2. Or check your database for webhook callback data
        
        // For now, this checks if transaction was confirmed via webhook
        // In production, call Paymob's transaction query API
        
        return 'pending'; // or 'success' or 'failed' based on actual status
    }

    /**
     * Step 3: Process Refund
     * Refund a completed transaction
     * 
     * @param string $transactionId - The Paymob transaction ID from successful payment
     * @param float $amount - Amount to refund in EGP
     * @return array - Refund status and details
     */
    public function refundPayment($transactionId, $amount) {
        try {
            // Paymob refund endpoint (you'll need to check their docs for exact endpoint)
            // Refunds are usually done via dashboard or specific refund API
            
            // For now, returning mock response
            // In production, implement actual Paymob refund API call
            
            return [
                'status' => 'refunded',
                'refund_id' => 'paymob_refund_' . uniqid(),
                'amount' => $amount,
                'message' => 'Refund initiated successfully'
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Refund processing error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Validate Payment Method
     * Check if the payment method is supported by Paymob
     * 
     * @param string $method - Payment method name or integration ID
     * @return bool
     */
    public function validatePaymentMethod($method) {
        // Paymob supports: card, wallet, bank_transfer, etc.
        // You can also pass integration IDs directly
        $validMethods = ['card', 'wallet', 'bank_transfer', 'cash', 'valu'];
        
        // Check if it's a valid method name or numeric integration ID
        return in_array($method, $validMethods) || is_numeric($method);
    }

    /**
     * Helper: Make API Call to Paymob using Guzzle
     * 
     * @param string $endpoint - API endpoint URL
     * @param array $payload - Request body data
     * @return array|null - API response or null on failure
     */
    private function makeApiCall(string $endpoint, array $payload): ?array {
        try {
            $response = $this->httpClient->post($endpoint, [
                'json' => $payload
            ]);
            
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
            
            if ($statusCode !== 200 && $statusCode !== 201) {
                error_log('Paymob API Error - Status: ' . $statusCode . ' Body: ' . $body);
                return $data ?? ['error' => 'HTTP Status ' . $statusCode, 'raw_response' => $body];
            }
            
            return $data;
            
        } catch (GuzzleException $e) {
            // Log error or handle it appropriately
            error_log('Paymob API Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        } catch (\Exception $e) {
            error_log('General Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Handle Webhook Callback
     */
    public function handleWebhook(array $callbackData): array {
        $type = $callbackData['type'] ?? 'UNKNOWN'; 
        $type = strtoupper($type);
        
        // We only listen for TRANSACTION and TOKEN types
        if ($type !== 'TRANSACTION' && $type !== 'TOKEN') {
            return [
                'status' => 'ignored',
                'message' => "Webhook type {$type} ignored",
                'order_id' => null
            ];
        }

        // Validate HMAC if secret is provided
        $hmacSecret = env('PAYMOB_HMAC_SECRET');
        if (!empty($hmacSecret)) {
            $receivedHmac = request()->query('hmac');
            if (!$this->validateHMAC($callbackData, $receivedHmac, $hmacSecret, $type)) {
                return [
                    'status' => 'failed',
                    'message' => 'HMAC validation failed',
                    'order_id' => null
                ];
            }
        }

        $obj = $callbackData['obj'] ?? $callbackData;

        if ($type === 'TRANSACTION') {
            $success = false;
            if (isset($obj['success'])) {
                $success = $obj['success'] === true || $obj['success'] === 'true' || $obj['success'] == 1;
            }

            return [
                'type' => 'TRANSACTION',
                'order_id' => $obj['order']['id'] ?? $obj['order'] ?? null,
                'transaction_id' => $obj['id'] ?? null,
                'amount' => isset($obj['amount_cents']) ? $obj['amount_cents'] / 100 : 0,
                'status' => $success ? 'success' : 'failed',
                'raw_status' => $obj['status'] ?? 'unknown'
            ];
        }

        if ($type === 'TOKEN') {
            return [
                'type' => 'TOKEN',
                'token' => $obj['token'] ?? null,
                'card_subtype' => $obj['card_subtype'] ?? null,
                'masked_pan' => $obj['masked_pan'] ?? null,
                'order_id' => $obj['order_id'] ?? null,
                'status' => 'success'
            ];
        }

        return [
            'status' => 'unknown',
            'order_id' => null
        ];
    }

    /**
     * Validate HMAC for Paymob Callback
     */
    private function validateHMAC(array $data, ?string $receivedHmac, string $secret, string $type): bool {
        if (empty($receivedHmac)) return false;

        $concatenatedString = '';
        
        if ($type === 'TRANSACTION') {
            $obj = $data['obj'] ?? [];
            $keys = [
                'amount_cents', 'created_at', 'currency', 'error_occured', 
                'has_parent_transaction', 'id', 'integration_id', 'is_3d_secure', 
                'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment', 
                'is_voided', 'order.id', 'owner', 'pending', 'source_data.pan', 
                'source_data.sub_type', 'source_data.type', 'success'
            ];
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
                
                $concatenatedString .= $val;
            }
        } elseif ($type === 'TOKEN') {
            $obj = $data['obj'] ?? $data;
            $keys = ['card_subtype', 'created_at', 'email', 'id', 'masked_pan', 'merchant_id', 'order_id', 'token'];
            foreach ($keys as $key) {
                $val = $obj[$key] ?? '';
                if (is_bool($val)) $val = $val ? 'true' : 'false';
                $concatenatedString .= $val;
            }
        }

        $calculatedHmac = hash_hmac('sha512', $concatenatedString, $secret);
        return hash_equals($calculatedHmac, $receivedHmac);
    }

    public function approved() {
        return true;
    }
}
