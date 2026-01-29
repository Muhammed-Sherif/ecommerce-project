<?php
namespace shared\events;

class CheckoutCompleted
{
    public $checkoutData;
    public $occurredOn;

    public function __construct(array $checkoutData)
    {
        $this->checkoutData = $checkoutData;
        $this->occurredOn = new \DateTimeImmutable();
    }
}
