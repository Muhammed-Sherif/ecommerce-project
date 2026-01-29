<?php
namespace shipments\application\commands;

use shipments\domains\contracts\Ishipment;

class UpdateShipmentStatusHandler {
    private $shipmentRepository;
    private $updateShipmentStatus;

    public function __construct(Ishipment $shipmentRepository, UpdateShipmentStatus $updateShipmentStatus) {
        $this->shipmentRepository = $shipmentRepository;
        $this->updateShipmentStatus = $updateShipmentStatus;
    }

    public function handle($id, string $status, array $meta = []) {
        $shipment = $this->shipmentRepository->findById($id);
        if (!$shipment) {
            return ['success' => false, 'message' => 'Shipment not found'];
        }
        $updated = $this->updateShipmentStatus::execute($shipment, $status, $meta);
        $this->shipmentRepository->save($updated);
        return ['success' => true, 'shipment' => $updated];
    }
}
