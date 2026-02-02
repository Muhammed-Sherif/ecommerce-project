<?php
namespace cart\application\commands;
use cart\domains\contracts\ICartRepository;
use shared\IEventBus;
use shared\events\CartItemAdded;
use cart\domains\contracts\IInventoryQueriesGetway;
class AddToCartHandler
{
    private $repository;
    private $addToCart;
    private $eventBus;
    private $inventoryQueries;

    public function __construct(ICartRepository $repository, AddToCart $addToCart , IEventBus $eventBus , IInventoryQueriesGetway $inventoryQueries)
    {
        $this->repository = $repository;
        $this->addToCart = $addToCart;
        $this->eventBus = $eventBus;
        $this->inventoryQueries = $inventoryQueries;
    }

    public function handle($userId, array $data)
    {
        
        $validated = $this->addToCart::execute($data);
        $this->checkIntventoryAvailability($validated['product_id'] , $validated['quantity']);
        $this->repository->addItem(
            $userId,
            $validated['product_id'],
            $validated['quantity'],
            $validated['coupon_id'] ?? null,
            $validated['coupon_code'] ?? null
        );
        return ['success' => true, 'message' => 'Added to cart'];
    }
    public function checkIntventoryAvailability ($productId, $quantity){

        $inventory = $this->inventoryQueries->getInventoryForProduct($productId);
        $available = $inventory ? (int) ($inventory->quantity ?? 0)  : 0;
        if ($available - $this->repository->getReservedQuantityInCart($productId) < $quantity) {
            throw new \RuntimeException('Insufficient stock for product ID: ' . $productId);
        }
    }
}
