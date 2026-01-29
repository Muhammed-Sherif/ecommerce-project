<?php
namespace shipments\domains\models;

class Shipment {
    public $id;
    public $orderId;
    public $userId;
    public $address; // associative array
    public $carrier;
    public $service;
    public $cost;
    public $currency;
    public $trackingNumber;
    public $trackingUrl;
    public $status;
    public $createdAt;
    public $updatedAt;
    public $shippedAt;
    public $deliveredAt;

    public function __construct(array $data) {
        $this->id = $data['id'] ?? null;
        $this->orderId = $data['orderId'];
        $this->userId = $data['userId'];
        $this->address = $data['address'];
        $this->carrier = $data['carrier'] ?? null;
        $this->service = $data['service'] ?? null;
        $this->cost = $data['cost'] ?? 0;
        $this->currency = $data['currency'] ?? null;
        $this->trackingNumber = $data['trackingNumber'] ?? null;
        $this->trackingUrl = $data['trackingUrl'] ?? null;
        $this->status = $data['status'] ?? 'pending';
        $this->createdAt = $data['createdAt'] ?? date('Y-m-d H:i:s');
        $this->updatedAt = $data['updatedAt'] ?? date('Y-m-d H:i:s');
        $this->shippedAt = $data['shippedAt'] ?? null;
        $this->deliveredAt = $data['deliveredAt'] ?? null;
    }
}
