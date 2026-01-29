<?php
namespace orders\application\queries;

use orders\domains\contracts\IOrderRepository;

class GetOrderHandler
{
    private $repository;
    private $getOrder;

    public function __construct(IOrderRepository $repository, GetOrder $getOrder)
    {
        $this->repository = $repository;
        $this->getOrder = $getOrder;
    }

    public function handle(array $data)
    {
        // Validate data
        $validatedData = $this->getOrder::execute($data);

        // Fetch order
        $order = $this->repository->findById($validatedData['order_id']);

        if (!$order) {
            throw new \RuntimeException('Order not found');
        }

        return [
            'success' => true,
            'order' => $order,
        ];
    }
}
