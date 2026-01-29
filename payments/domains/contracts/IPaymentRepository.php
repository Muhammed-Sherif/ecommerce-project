<?php
namespace payments\domains\contracts;

interface IPaymentRepository
{
    public function create(array $paymentData);
    public function findByOrderId($orderId);
    public function update($id, array $data);
}
