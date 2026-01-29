<?php
namespace orders\application\commands;

use orders\domains\models\Order;
use orders\domains\models\OrderItem;
class CreateOrder
{
    public static function execute(array $orderData): array
    {  
        $orderItems = [];
        foreach ($orderData['items'] as $item) {
            $orderItems[] = OrderItem::fromArray($item);
        }
        $orderData['items'] = $orderItems;
        $order = Order::fromArray($orderData);
        return [
            "order" => $order,
            "shipping_address" => [
                "street" => $order->shippingStreet,
                "city" => $order->shippingCity,
                "state" => $order->shippingState,
                "country" => $order->shippingCountry,
                "zip_code" => $order->shippingZipCode,
            ],
        ];
    }
}
