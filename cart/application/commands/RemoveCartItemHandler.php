<?php
namespace cart\application\commands;

use cart\domains\contracts\ICartRepository;

class RemoveCartItemHandler
{
    private $repository;
    private $removeCartItem;

    public function __construct(ICartRepository $repository, RemoveCartItem $removeCartItem)
    {
        $this->repository = $repository;
        $this->removeCartItem = $removeCartItem;
    }

    public function handle($userId, array $data)
    {
        $validated = $this->removeCartItem::execute($data);
        $exists = $this->repository->getCart($userId)->firstWhere('product_id', $validated['product_id']);
        if (!$exists) {
            return ['success' => false, 'message' => 'Item not in cart'];
        }
        $this->repository->removeItem($userId, $validated['product_id']);
        return ['success' => true, 'message' => 'Item removed'];
    }
}
