<?php
namespace orders\domains\contracts;

interface IOrderRepository
{
    public function create(array $orderData, array $items);
    public function update($id, array $orderData);
    public function findById($id);
    public function findByCustomerId($customerId);
    public function getAll(array $filters = []);
    public function findByGatewayOrderId($gatewayOrderId);
    public function getCustomersForVendor($vendorId = null, $status = 'paid');
}
