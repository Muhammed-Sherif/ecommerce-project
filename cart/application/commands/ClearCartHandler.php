<?php
namespace cart\application\commands;

use cart\domains\contracts\ICartRepository;

class ClearCartHandler
{
    private $repository;

    public function __construct(ICartRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle($userId)
    {
        $this->repository->clearCart($userId);
        return ['success' => true, 'message' => 'Cart cleared'];
    }
}
