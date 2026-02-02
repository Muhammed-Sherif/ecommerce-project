<?php
namespace orders\infrastructure\getway;

use orders\domains\contracts\ICartGateway;
use cart\shared\CartApi;

class CartGateway implements ICartGateway
{
    private $cartApi;

    public function __construct(CartApi $cartApi)
    {
        $this->cartApi = $cartApi;
    }

    public function getCart($userId)
    {
        return $this->cartApi->getCart($userId);
    }

    public function clearCart($userId)
    {
        return $this->cartApi->clearCart($userId);
    }
}