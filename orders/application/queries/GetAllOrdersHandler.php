<?php
namespace orders\application\queries;

use orders\domains\contracts\IOrderRepository;

class GetAllOrdersHandler
{
    private $repository;
    private $getAllOrders;

    public function __construct(IOrderRepository $repository, GetAllOrders $getAllOrders)
    {
        $this->repository = $repository;
        $this->getAllOrders = $getAllOrders;
    }

    public function handle(array $data)
    {
        // Validate and prepare filters
        $filters = $this->getAllOrders::execute($data);

        // Fetch orders
        $orders = $this->repository->getAll($filters);

        return [
            'success' => true,
            'orders' => $orders,
        ];
    }
}
