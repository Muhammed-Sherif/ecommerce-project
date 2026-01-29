<?php
namespace shipments\application\commands;

use shipments\domains\contracts\Ishipment;

class CreateShipmentHandler {
    private $shipmentRepository;
    private $createShipment;
    private $defaultCurrency;

    public function __construct(Ishipment $shipmentRepository, CreateShipment $createShipment, string $defaultCurrency = 'USD') {
        $this->shipmentRepository = $shipmentRepository;
        $this->createShipment = $createShipment;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @param array $data
     * @param callable|null $orderValidator Optional: function(string $orderId): bool
     */
    public function handle(array $data, callable $orderValidator = null) {
        if ($orderValidator && isset($data['orderId']) && !$orderValidator($data['orderId'])) {
            return ['success' => false, 'message' => 'Order not found'];
        }
        $shipment = $this->createShipment::execute($data, $this->defaultCurrency);
        $this->shipmentRepository->save($shipment);
        return ['success' => true, 'shipment' => $shipment];
    }
}
