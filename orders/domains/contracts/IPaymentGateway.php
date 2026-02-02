<?php
namespace orders\domains\contracts;

interface IPaymentGateway
{
    public function getPaymentLink($country, float $amount, string $currency, string $orderId);
}