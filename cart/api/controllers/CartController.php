<?php
namespace cart\api\controllers;

use cart\application\commands\AddToCartHandler;
use cart\application\commands\UpdateCartItemHandler;
use cart\application\commands\RemoveCartItemHandler;
use cart\application\commands\ClearCartHandler;
use cart\application\queries\GetCartHandler;

class CartController
{
    public function index($userId, GetCartHandler $handler)
    {
        return $handler->handle($userId);
    }

    public function store($userId, array $data, AddToCartHandler $handler)
    {
        try {
            return $handler->handle($userId, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($userId, array $data, UpdateCartItemHandler $handler)
    {
        try {
            return $handler->handle($userId, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function destroy($userId, array $data, RemoveCartItemHandler $handler)
    {
        try {
            return $handler->handle($userId, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function clear($userId, ClearCartHandler $handler)
    {
        return $handler->handle($userId);
    }
}
