<?php
namespace orders\application\queries;

use orders\domains\contracts\IOrderRepository;

class GetOrderCustomersHandler
{
    private $repository;

    public function __construct(IOrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($vendorId = null, $status = 'paid')
    {
        $customers = $this->repository->getCustomersForVendor($vendorId, $status);

        return [
            'success' => true,
            'customers' => $customers,
        ];
    }
}