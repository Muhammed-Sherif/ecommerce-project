<?php
namespace payments\domains\contracts;

interface PaymentGatewayStrategy
{
    public function createPaymentSession($amount, $currency, $orderId): array;
    public function handleWebhook(array $data): array;
}