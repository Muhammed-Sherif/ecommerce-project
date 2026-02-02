<?php
namespace reviews\infrastructure\getway;

use reviews\domains\contracts\IOrdersQueriesGetway;
use reviews\infrastructure\dtos\OrdersQueriesResultDto;
use orders\shared\OrdersQueriesApiImp as OrdersQueriesApiImp;

class OrdersQueriesGetway implements IOrdersQueriesGetway
{
    /** @var OrdersQueriesApiImp */
    private $ordersApi;

    public function __construct(OrdersQueriesApiImp $ordersApi)
    {
        $this->ordersApi = $ordersApi;
    }

    public function hasDeliveredProductForCustomer($customerId, $productId)
    {
        $result = $this->ordersApi->hasDeliveredProductForCustomer($customerId, $productId);
        return OrdersQueriesResultDto::fromResult($result);
    }
}
