<?php
namespace orders\internal;

interface IOrdersQueriesApi {
    public function hasDeliveredProductForCustomer($customerId, $productId);
}