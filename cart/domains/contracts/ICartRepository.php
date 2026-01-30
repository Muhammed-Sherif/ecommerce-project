<?php
namespace cart\domains\contracts;

interface ICartRepository
{
    public function addItem($userId, $productId, int $quantity, ?int $couponId = null, ?string $couponCode = null);
    public function updateItem($userId, $productId, int $quantity, ?int $couponId = null, ?string $couponCode = null);
    public function removeItem($userId, $productId);
    public function clearCart($userId);
    public function getCart($userId);
}
