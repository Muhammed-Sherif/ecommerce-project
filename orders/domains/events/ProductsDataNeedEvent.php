<?php   
namespace orders\domains\events;

class ProductsDataNeedEvent
{
    public $orderId;
    public $productIds;
    public function __construct($orderId, array $productIds)
    {
        $this->orderId = $orderId;
        $this->productIds = $productIds;
    }
}