<?php
namespace cart\application\commands;
use cart\domains\contracts\ICartRepository;
use shared\IEventBus;
use shared\events\CartItemAdded;
class AddToCartHandler
{
    private $repository;
    private $addToCart;
    private $eventBus;

    public function __construct(ICartRepository $repository, AddToCart $addToCart , IEventBus $eventBus)
    {
        $this->repository = $repository;
        $this->addToCart = $addToCart;
        $this->eventBus = $eventBus;
    }

    public function handle($userId, array $data)
    {
        $validated = $this->addToCart::execute($data);
        $this->repository->addItem(
            $userId,
            $validated['product_id'],
            $validated['quantity'],
            $validated['coupon_id'] ?? null,
            $validated['coupon_code'] ?? null
        );
        return ['success' => true, 'message' => 'Added to cart'];
    }
}
