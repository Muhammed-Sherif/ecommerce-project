<?php
namespace inventory\shared;

interface IInventoryQueriesGetway
{
    public function getInventoryForProduct($productId);
}
