<?php
namespace shipments\application\queries;

use shipments\domains\contracts\Ishipment;

class GetShipmentsByOrderHandler {
    private $shipmentRepository;
    private $getShipmentsByOrder;

    public function __construct(Ishipment $shipmentRepository, GetShipmentsByOrder $getShipmentsByOrder) {
        $this->shipmentRepository = $shipmentRepository;
        $this->getShipmentsByOrder = $getShipmentsByOrder;
    }

    public function handle($orderId) {
        $shipments = $this->shipmentRepository->findByOrderId($orderId);
        return ['success' => true, 'shipments' => $this->getShipmentsByOrder::execute($shipments ?? [])];
    }
}
