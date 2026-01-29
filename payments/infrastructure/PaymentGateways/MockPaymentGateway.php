<?php
namespace payments\infrastructure\PaymentGateways;

use payments\domains\contracts\PaymentGatewayStrategy;

class MockPaymentGateway implements PaymentGatewayStrategy
{
    public function createPaymentSession($amount, $currency, $orderId): array
    {
        // Simulate a payment link
        return [
            'link' => "https://mock-payment-provider.com/pay?amount={$amount}&currency={$currency}&order={$orderId}",
            'gateway_order_id' => 'mock_order_' . $orderId,
            'status' => 'pending'
        ];
    }

    public function handleWebhook(array $data): array
    {
        return [
            'order_id' => $data['order_id'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? 'mock_' . uniqid(),
            'amount' => $data['amount'] ?? 0,
            'status' => $data['status'] ?? 'success', // Mock default to success
        ];
    }
}
