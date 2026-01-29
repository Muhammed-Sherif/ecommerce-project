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
        return ['success' => true, 'cart' => $this->getCart::execute($items ?? [])];
    }
}
