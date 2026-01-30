<?php
namespace cart\domains\models;
class Cart
{
    public $id;
    public $userId;
    public $items;
    public function __construct(int $id = null , int $userId, array $items)
    {
        $this->id = $id ?? null;
        $this->userId = $userId;
        $this->items = $items;
    }
    public function addItem(int $productId, int $quantity, ?int $couponId = null, ?string $couponCode = null)
    {
        $item = [
            'product_id' => $productId,
            'quantity' => $quantity,
        ];
        if ($couponId !== null) {
            $item['coupon_id'] = $couponId;
        }
        if ($couponCode !== null) {
            $item['coupon_code'] = $couponCode;
        }
        $this->items[] = $item;
    }
    public function updateItem(int $productId, int $quantity)
    {
        foreach ($this->items as &$item) {
            if ($item['product_id'] == $productId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }
    public function removeItem(int $productId)
    {
        $this->items = array_filter($this->items, function ($item) use ($productId) {
            return $item['product_id'] != $productId;
        });
    }
    public function clearCart()
    {
        $this->items = [];
    }
    public function getItems()
    {
        return $this->items;
    }
    public function toArray()
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'items' => $this->items,
        ];
    }
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['userId'],
            $data['items'],
            $data['createdAt'],
            $data['updatedAt']
        );
    }
}
