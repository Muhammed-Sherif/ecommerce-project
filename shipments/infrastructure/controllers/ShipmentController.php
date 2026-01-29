<?php
namespace shipments\infrastructure\controllers;

use shipments\application\commands\CreateShipmentHandler;
use shipments\application\commands\UpdateShipmentStatusHandler;
use shipments\application\commands\AssignTrackingNumberHandler;
use shipments\application\queries\GetShipmentHandler;
use shipments\application\queries\ListShipmentsHandler;
use shipments\application\queries\GetShipmentsByOrderHandler;

class ShipmentController
{
    /**
     * Create a new shipment
     */
    public function create(array $data, CreateShipmentHandler $handler, callable $orderExists)
    {
        // $orderExists must be a callable that returns bool for a given orderId
        return $handler->handle($data, $orderExists);
    }

    /**
     * Update shipment status
     */
    public function updateStatus($id, string $status, UpdateShipmentStatusHandler $handler)
    {
        return $handler->handle($id, $status);
    }

    /**
     * Assign tracking number (and optional URL)
     */
    public function assignTracking($id, string $trackingNumber, ?string $trackingUrl, AssignTrackingNumberHandler $handler)
    {
        return $handler->handle($id, $trackingNumber, $trackingUrl);
    }

    /**
     * Get shipment by ID
     */
    public function getById($id, GetShipmentHandler $handler)
    {
        return $handler->handle($id);
    }

    /**
     * List shipments
     */
    public function getAll(ListShipmentsHandler $handler)
    {
        return $handler->handle();
    }

    /**
     * List shipments by Order ID
     */
    public function getByOrder($orderId, GetShipmentsByOrderHandler $handler)
    {
        return $handler->handle($orderId);
    }
}
