<?php
namespace Coupons\infrastructure\getway;

use Coupons\domains\contracts\ICartGateway;
use cart\shared\CartApi;

class CartGateway implements ICartGateway
{
    private $cartApi;

    public function __construct(CartApi $cartApi)
    {
        $this->cartApi = $cartApi;
    }

    /**
     * Get cart for user - Gateway only handles cart communication
     * @param int $userId
     * @return array
     */
    public function getCart($userId)
    {
        return $this->cartApi->getCart($userId);
    }
}