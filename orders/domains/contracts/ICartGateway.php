<?php
namespace orders\domains\contracts;

interface ICartGateway
{
    public function getCart($userId);
    public function clearCart($userId);
}