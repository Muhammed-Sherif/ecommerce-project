<?php
namespace accounts\domains\contracts;

interface IShippingAddressRepository
{
    public function upsert($userId, array $data);
    public function findByUserId($userId);
}
