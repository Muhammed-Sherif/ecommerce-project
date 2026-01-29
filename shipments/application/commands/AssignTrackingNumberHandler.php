<?php
namespace shipments\application\commands;

use shipments\domains\contracts\Ishipment;

class AssignTrackingNumberHandler {
    private $shipmentRepository;
    private $assignTracking;

    public function __construct(Ishipment $shipmentRepository, AssignTrackingNumber $assignTracking) {
        $this->shipmentRepository = $shipmentRepository;
        $this->assignTracking = $assignTracking;
    }

    public function handle($id, string $trackingNumber, ?string $trackingUrl = null) {
        $shipment = $this->shipmentRepository->findById($id);
        if (!$shipment) {
            return ['success' => false, 'message' => 'Shipment not found'];
        }
        $updated = $this->assignTracking::execute($shipment, $trackingNumber, $trackingUrl);
        $this->shipmentRepository->save($updated);
        return ['success' => true, 'shipment' => $updated];
    }
}
