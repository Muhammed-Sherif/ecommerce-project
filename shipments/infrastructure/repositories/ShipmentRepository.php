<?php

namespace shipments\infrastructure\repositories;

use shipments\domains\contracts\Ishipment;
use shipments\domains\models\Shipment;
use Illuminate\Support\Facades\DB;

class ShipmentRepository implements Ishipment
{
    public function save($shipment)
    {
        $data = [
            'order_id' => $shipment->orderId,
            'user_id' => $shipment->userId,
            'address' => json_encode($shipment->address),
            'carrier' => $shipment->carrier,
            'service' => $shipment->service,
            'cost' => $shipment->cost,
            'currency' => $shipment->currency,
            'tracking_number' => $shipment->trackingNumber,
            'tracking_url' => $shipment->trackingUrl,
            'status' => $shipment->status,
            'shipped_at' => $shipment->shippedAt,
            'delivered_at' => $shipment->deliveredAt,
            'created_at' => $shipment->createdAt,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($shipment->id) {
            DB::table('shipments')->where('id', $shipment->id)->update($data);
            return $shipment->id;
        }

        $id = DB::table('shipments')->insertGetId($data);
        $shipment->id = $id;
        return $id;
    }

    public function findById($id)
    {
        $data = DB::table('shipments')->where('id', $id)->first();
        return $data ? $this->mapToModel($data) : null;
    }

    public function findAll()
    {
        $items = DB::table('shipments')->get();
        return $items->map(fn($item) => $this->mapToModel($item));
    }

    public function findByOrderId($orderId)
    {
        $data = DB::table('shipments')->where('order_id', $orderId)->first();
        return $data ? $this->mapToModel($data) : null;
    }

    public function updateStatus($id, $status, $meta = [])
    {
        $updateData = ['status' => $status, 'updated_at' => now()];
        
        if ($status === 'shipped') {
            $updateData['shipped_at'] = now();
        } elseif ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        if (isset($meta['tracking_number'])) {
            $updateData['tracking_number'] = $meta['tracking_number'];
        }
        if (isset($meta['tracking_url'])) {
            $updateData['tracking_url'] = $meta['tracking_url'];
        }

        return DB::table('shipments')->where('id', $id)->update($updateData);
    }

    private function mapToModel($data)
    {
        return new Shipment([
            'id' => $data->id,
            'orderId' => $data->order_id,
            'userId' => $data->user_id,
            'address' => json_decode($data->address, true),
            'carrier' => $data->carrier,
            'service' => $data->service,
            'cost' => $data->cost,
            'currency' => $data->currency,
            'trackingNumber' => $data->tracking_number,
            'trackingUrl' => $data->tracking_url,
            'status' => $data->status,
            'createdAt' => $data->created_at,
            'updatedAt' => $data->updated_at,
            'shippedAt' => $data->shipped_at,
            'deliveredAt' => $data->delivered_at,
        ]);
    }
}
