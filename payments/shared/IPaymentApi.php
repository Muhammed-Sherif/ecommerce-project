<?php
namespace payments\shared;

interface IPaymentApi
{
    /**
     * @param string $location
     * @param float $amount
     * @param string $currency
     * @param string $orderId
     * @return array Contains 'link' and 'gateway_order_id'
     */
    public function getPaymentLink(string $location, float $amount, string $currency, string $orderId): array;
}
