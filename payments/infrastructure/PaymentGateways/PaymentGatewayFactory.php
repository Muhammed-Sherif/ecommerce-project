<?php
namespace payments\infrastructure\PaymentGateways;

use payments\domains\contracts\PaymentGatewayStrategy;

class PaymentGatewayFactory
{
    public static function create($identifier): PaymentGatewayStrategy
    {   
        return match (strtolower($identifier)) {
            "egypt", "paymob" => new PaymobGateway(),
            "paymob" => new PaymobGateway(),
            "mock" => new MockPaymentGateway(),
            default => throw new \InvalidArgumentException("Unsupported payment gateway or location: {$identifier}")
        }; 
    }
}