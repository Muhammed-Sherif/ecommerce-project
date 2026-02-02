<?php
namespace shared\events;

class ProductDeleted
{
    public $productId;
    public $occurredOn;

    public function __construct($productId)
    {
        $this->productId = $productId;
        $this->occurredOn = new \DateTimeImmutable();
    }
}
