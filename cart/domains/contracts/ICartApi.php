<?php 

namespace cart\domains\contracts;

interface ICartApi
{ 
    public function getCart($UserId);
    public function clearCart($UserId);
    
}   