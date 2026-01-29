<?php
namespace shared\events;

class OrderCreated
{
    public $orderId;
    public $amount;
    public $currency;
    public $customerEmail;

    public function __construct($orderId, $amount, $currency = 'USD', $customerEmail = null)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->customerEmail = $customerEmail;
    }
}
