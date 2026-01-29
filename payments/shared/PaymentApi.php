<?php
namespace payments\shared;

use payments\domains\models\PaymentGatewayContext;
use payments\infrastructure\PaymentGateways\PaymentGatewayFactory;

class PaymentApi implements IPaymentApi
{

    public function getPaymentLink(string $location, float $amount, string $currency, string $orderId): array
    {
        $context = new PaymentGatewayContext(PaymentGatewayFactory::create($location));
        $response = $context->pay($amount, $currency, $orderId);
        \Log::info('Checkout URL: from the payment linkfucntion ' . ($response['checkout_url'] ?? $response['link'] ?? ''));
        return [
            'link' => $response['checkout_url'] ?? $response['link'] ?? '',
            'gateway_order_id' => $response['gateway_order_id'] ?? null
        ];
    }
}
