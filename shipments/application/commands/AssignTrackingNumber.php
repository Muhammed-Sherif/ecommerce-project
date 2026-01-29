<?php
namespace shipments\application\commands;

class AssignTrackingNumber {
    public static function execute($shipment, string $trackingNumber, ?string $trackingUrl = null) {
        if (empty($trackingNumber)) {
            throw new \InvalidArgumentException('Tracking number required');
        }
        if (is_array($shipment)) {
            $shipment['trackingNumber'] = $trackingNumber;
            $shipment['trackingUrl'] = $trackingUrl;
            $shipment['updatedAt'] = date('Y-m-d H:i:s');
            return $shipment;
        }
        $shipment->trackingNumber = $trackingNumber;
        $shipment->trackingUrl = $trackingUrl;
        $shipment->updatedAt = date('Y-m-d H:i:s');
        return $shipment;
    }
}
