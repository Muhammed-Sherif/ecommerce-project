<?php
namespace accounts\infrastructure\repositries;

use accounts\domains\contracts\IShippingAddressRepository;
use App\Models\User;

class ShippingAddressRepository implements IShippingAddressRepository
{
    public function upsert($userId, array $data)
    {
        return User::query()->where('id', $userId)->update([
            'name' => $data['full_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'shipping_street' => $data['street'] ?? null,
            'shipping_city' => $data['city'] ?? null,
            'shipping_state' => $data['state'] ?? null,
            'shipping_country' => $data['country'] ?? null,
            'shipping_zip_code' => $data['zip_code'] ?? null,
            'updated_at' => now(),
        ]);
    }

    public function findByUserId($userId)
    {
        $user = User::query()->where('id', $userId)->first();
        if (!$user) return null;
        
        return (object)[
            'full_name' => $user->name,
            'phone' => $user->phone,
            'street' => $user->shipping_street,
            'city' => $user->shipping_city,
            'state' => $user->shipping_state,
            'country' => $user->shipping_country,
            'zip_code' => $user->shipping_zip_code,
        ];
    }
}
