<?php 

namespace cart\shared;

use cart\application\queries\GetCartHandler;
use cart\application\commands\ClearCartHandler;
use cart\domains\contracts\ICartApi;

class CartApi implements ICartApi
{ 
    private $getCartHandler;
    private $clearCartHandler;

    public function __construct(GetCartHandler $getCartHandler, ClearCartHandler $clearCartHandler)
    {
        $this->getCartHandler = $getCartHandler;
        $this->clearCartHandler = $clearCartHandler;
    }

    public function getCart($UserId) {
        $result = $this->getCartHandler->handle($UserId);
        if ($result["success"]) {
            return $result;
        }
        return null;
    } 

    public function clearCart($UserId) {
        $result = $this->clearCartHandler->handle($UserId);
        if ($result["success"]) {
            return $result;
        }
        return null;
    } 
}   