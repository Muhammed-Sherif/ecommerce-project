<?php
namespace cart\infrastructure\repositories;

use cart\domains\contracts\ICartRepository;
use App\Models\CartItem;

class CartRepository implements ICartRepository
{
    public function addItem($userId, $productId, int $quantity, ?int $couponId = null, ?string $couponCode = null)
    {
        \Log::info('Adding item to cart: userId=' . $userId . ', productId=' . $productId . ', quantity=' . $quantity . ', couponId=' . $couponId . ', couponCode=' . $couponCode);
        $existing = CartItem::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return $this->updateItem($userId, $productId, $existing->quantity + $quantity, $couponId, $couponCode);
        }

        $item = CartItem::query()->create([
            'user_id' => $userId,
            'product_id' => $productId,
            'coupon_id' => $couponId,
            'coupon_code' => $couponCode,
            'quantity' => $quantity,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return $item->id;
    }

    public function updateItem($userId, $productId, int $quantity, ?int $couponId = null, ?string $couponCode = null)
    {
        return CartItem::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->update($this->buildUpdatePayload($quantity, $couponId, $couponCode));
    }

    public function removeItem($userId, $productId)
    {
        return CartItem::query()
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
    }

    public function clearCart($userId)
    {
        return CartItem::query()
            ->where('user_id', $userId)
            ->delete();
    }

    public function getCart($userId)
    {
        return CartItem::query()
            ->join('products', 'cart_items.product_id', '=', 'products.id')
            ->where('cart_items.user_id', $userId)
            ->select('cart_items.*', 'products.name', 'products.price', 'products.image', 'products.user_id')
            ->get();
    }
    public function getReservedQuantityInCart( $productId)
    {
        return CartItem::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->sum('quantity');
    }
    private function buildUpdatePayload(int $quantity, ?int $couponId, ?string $couponCode): array
    {
        $update = [
            'quantity' => $quantity,
            'updated_at' => now(),
        ];
        if ($couponId !== null) {
            $update['coupon_id'] = $couponId;
        }
        if ($couponCode !== null) {
            $update['coupon_code'] = $couponCode;
        }
        return $update;
    }
}
