<?php
namespace accounts\application\commands;

use accounts\domains\contracts\IShippingAddressRepository;

class UpsertShippingAddressHandler
{
    private $repository;
    private $upsertShippingAddress;

    public function __construct(IShippingAddressRepository $repository, UpsertShippingAddress $upsertShippingAddress)
    {
        $this->repository = $repository;
        $this->upsertShippingAddress = $upsertShippingAddress;
    }

    public function handle($userId, array $data)
    {
        $payload = $this->upsertShippingAddress::execute($data);
        $this->repository->upsert($userId, $payload);
        $saved = $this->repository->findByUserId($userId);
        return ['success' => true, 'shipping' => $saved];
    }
}
