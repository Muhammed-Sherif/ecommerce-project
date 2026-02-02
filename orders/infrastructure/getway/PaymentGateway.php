<?php
namespace orders\infrastructure\getway;

use orders\domains\contracts\IPaymentGateway;
use payments\shared\PaymentApi;

class PaymentGateway implements IPaymentGateway
{
    private $paymentApi;

    public function __construct(PaymentApi $paymentApi)
    {
        $this->paymentApi = $paymentApi;
    }

    public function getPaymentLink($country, float $amount, string $currency, string $orderId)
    {
        return $this->paymentApi->getPaymentLink($country, $amount, $currency, $orderId);
    }
}