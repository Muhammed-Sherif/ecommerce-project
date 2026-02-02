<?php
namespace reviews\domains\contracts;

interface IOrdersQueriesGetway
{
    public function hasDeliveredProductForCustomer($customerId, $productId);
}
