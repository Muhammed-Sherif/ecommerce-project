<?php
namespace cart\application\queries;

use cart\domains\contracts\ICartRepository;

class GetCartHandler
{
    private $repository;
    private $getCart;

    public function __construct(ICartRepository $repository, GetCart $getCart)
    {
        $this->repository = $repository;
        $this->getCart = $getCart;
    }

    public function handle($userId)
    {
        $items = $this->repository->getCart($userId);
        $cartItems = $this->getCart::execute($items ?? []);
        $total = 0;
        foreach ($cartItems as $item) {
            $price = isset($item->price) ? (float) $item->price : 0;
            $quantity = isset($item->quantity) ? (int) $item->quantity : 0;
            $total += $price * $quantity;
        }

        return [
            'success' => true,
            'cart' => $cartItems,
            'total_amount' => $total
        ];
    }
}
