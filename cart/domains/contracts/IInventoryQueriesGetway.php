<?php 
namespace cart\domains\contracts;
interface IInventoryQueriesGetway 
{
    public function getInventoryForProduct($productId);
}