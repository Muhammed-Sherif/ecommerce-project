<?php
namespace cart\infrastructure\repositories;

use cart\domains\contracts\ICartRepository;
use Illuminate\Support\Facades\DB;

class CartRepository implements ICartRepository
{
    public function addItem($userId, $productId, int $quantity)
    {
        $existing = DB::table('cart_items')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return $this->updateItem($userId, $productId, $existing->quantity + $quantity);
        }

        return DB::table('cart_items')->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function updateItem($userId, $productId, int $quantity)
    {
        return DB::table('cart_items')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->update([
                'quantity' => $quantity,
                'updated_at' => now(),
            ]);
    }

    public function removeItem($userId, $productId)
    {
        return DB::table('cart_items')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }

    public function clearCart($userId)
    {
        return DB::table('cart_items')
            ->where('user_id', $userId)
            ->delete();
    }

    public function getCart($userId)
    {
        return DB::table('cart_items')
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.user_id', $userId)
            ->select('cart_items.*', 'products.name', 'products.price', 'products.image')
            ->get();
    }
}
