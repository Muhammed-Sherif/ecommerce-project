<?php
namespace shipments\domains\contracts;

interface Ishipment {
    public function save($shipment);
    public function findById($id);
    public function findAll();
    public function findByOrderId($orderId);
    public function updateStatus($id, $status, $meta = []);
}
