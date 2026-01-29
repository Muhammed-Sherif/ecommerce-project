<?php
namespace shipments\application\queries;

use shipments\domains\contracts\Ishipment;

class ListShipmentsHandler {
    private $shipmentRepository;
    private $listShipments;

    public function __construct(Ishipment $shipmentRepository, ListShipments $listShipments) {
        $this->shipmentRepository = $shipmentRepository;
        $this->listShipments = $listShipments;
    }

    public function handle() {
        $shipments = $this->shipmentRepository->findAll();
        return ['success' => true, 'shipments' => $this->listShipments::execute($shipments ?? [])];
    }
}
