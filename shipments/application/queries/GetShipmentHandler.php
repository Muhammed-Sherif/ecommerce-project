<?php
namespace shipments\application\queries;

use shipments\domains\contracts\Ishipment;

class GetShipmentHandler {
    private $shipmentRepository;
    private $getShipment;

    public function __construct(Ishipment $shipmentRepository, GetShipment $getShipment) {
        $this->shipmentRepository = $shipmentRepository;
        $this->getShipment = $getShipment;
    }

    public function handle($id) {
        $shipment = $this->shipmentRepository->findById($id);
        if (!$shipment) {
            return ['success' => false, 'message' => 'Shipment not found'];
        }
        return ['success' => true, 'shipment' => $this->getShipment::execute($shipment)];
    }
}
