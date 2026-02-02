<?php 
namespace products\domains\contracts;
interface IInventoryCommandsGetway
{
    public function updateInventoryForProduct($productId, array $data);
    public function createInventoryForProduct($productId, array $data);
}