<?php
namespace shipments\application\commands;

use shipments\domains\models\Shipment;

class CreateShipment {
    public static function execute(array $data, string $defaultCurrency): Shipment {
        $required = ['orderId','userId','address'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Missing field: $field");
            }
        }
        if (!is_array($data['address'])) {
            throw new \InvalidArgumentException('Address must be an object');
        }
        $shipment = new Shipment([
            'orderId' => $data['orderId'],
            'userId' => $data['userId'],
            'address' => $data['address'],
            'carrier' => $data['carrier'] ?? null,
            'service' => $data['service'] ?? null,
            'cost' => $data['cost'] ?? 0,
            'currency' => $data['currency'] ?? $defaultCurrency,
            'status' => 'pending',
        ]);
        return $shipment;
    }
}
