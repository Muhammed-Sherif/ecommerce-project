<?php
namespace payments\domains\models;
use payments\domains\contracts\PaymentGatewayStrategy;

class PaymentGatewayContext
{
    private $strategy;
    public function __construct(PaymentGatewayStrategy $strategy) {
        $this->strategy = $strategy;
    }
    public function pay($amount , $currency , $orderId){
        return $this->strategy->createPaymentSession( $amount , $currency , $orderId );
    }
    public function handleWebhook(array $data) {
        return $this->strategy->handleWebhook($data);
    }
}