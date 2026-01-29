<?php
namespace accounts\application\queries;

use accounts\domains\contracts\IShippingAddressRepository;

class GetShippingAddressHandler
{
    private $repository;

    public function __construct(IShippingAddressRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($userId)
    {
        $shipping = $this->repository->findByUserId($userId);
        return ['success' => true, 'shipping' => $shipping];
    }
}
