<?php
namespace accounts\application\commands;

class UpsertShippingAddress
{
    public static function execute(array $data): array
    {
        if (empty($data['street']) || empty($data['city']) || empty($data['country']) || empty($data['zip_code'])) {
            throw new \InvalidArgumentException('Street, city, country, and zip code are required');
        }

        return [
            'full_name' => $data['full_name'] ?? '',
            'phone' => $data['phone'] ?? '',
            'street' => $data['street'],
            'city' => $data['city'],
            'state' => $data['state'] ?? '',
            'country' => $data['country'],
            'zip_code' => $data['zip_code'],
        ];
    }
}
