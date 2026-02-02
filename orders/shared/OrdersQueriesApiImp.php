<?php
namespace orders\shared;

use orders\internal\IOrdersQueriesApi;
use orders\domains\models\OrderStatus;
use orders\domains\contracts\IOrderRepository as OrderRepository;

class OrdersQueriesApiImp implements IOrdersQueriesApi
{
    private $repository;

    public function __construct(OrderRepository $repository) {
        $this->repository = $repository;
    }
    public function hasDeliveredProductForCustomer($customerId, $productId)
    {
        if (empty($customerId) || empty($productId)) {
            throw new \InvalidArgumentException('Invalid arguments provided to hasDeliveredProductForCustomer');
        }

        return $this->repository->checkForDeliveredOrderForUser($customerId, $productId);
    }
}
