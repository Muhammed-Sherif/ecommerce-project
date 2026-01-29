<?php
namespace shipments\application\commands;

class UpdateShipmentStatus {
    public static $allowed = [
        'pending','packed','shipped','in_transit','out_for_delivery','delivered','failed','canceled','returned'
    ];

    public static function execute($shipment, string $status, array $meta = []) {
        if (!in_array($status, self::$allowed, true)) {
            throw new \InvalidArgumentException('Invalid shipment status');
        }
        if (is_array($shipment)) {
            $shipment['status'] = $status;
            $shipment['updatedAt'] = date('Y-m-d H:i:s');
            if ($status === 'shipped') $shipment['shippedAt'] = date('Y-m-d H:i:s');
            if ($status === 'delivered') $shipment['deliveredAt'] = date('Y-m-d H:i:s');
            return $shipment;
        }
        $shipment->status = $status;
        $shipment->updatedAt = date('Y-m-d H:i:s');
        if ($status === 'shipped') $shipment->shippedAt = date('Y-m-d H:i:s');
        if ($status === 'delivered') $shipment->deliveredAt = date('Y-m-d H:i:s');
        return $shipment;
    }
}
