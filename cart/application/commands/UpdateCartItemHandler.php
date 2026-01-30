<?php
namespace cart\application\commands;

use cart\domains\contracts\ICartRepository;

class UpdateCartItemHandler
{
    private $repository;
    private $updateCartItem;

    public function __construct(ICartRepository $repository, UpdateCartItem $updateCartItem)
    {
        $this->repository = $repository;
        $this->updateCartItem = $updateCartItem;
    }

    public function handle($userId, array $data)
    {
        $validated = $this->updateCartItem::execute($data);
        $exists = $this->repository->getCart($userId)->firstWhere('product_id', $validated['product_id']);
        if (!$exists) {
            return ['success' => false, 'message' => 'Item not in cart'];
        }
        $this->repository->updateItem(
            $userId,
            $validated['product_id'],
            $validated['quantity'],
            $validated['coupon_id'] ?? null,
            $validated['coupon_code'] ?? null
        );
        return ['success' => true, 'message' => 'Cart updated'];
    }
}
