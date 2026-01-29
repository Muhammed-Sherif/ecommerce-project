<?php
namespace accounts\api\controllers;

use accounts\application\commands\UpsertShippingAddressHandler;
use accounts\application\queries\GetShippingAddressHandler;

class ProfileController
{
    public function getShipping($userId, GetShippingAddressHandler $handler)
    {
        return $handler->handle($userId);
    }

    public function updateShipping($userId, array $data, UpsertShippingAddressHandler $handler)
    {
        try {
            return $handler->handle($userId, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
