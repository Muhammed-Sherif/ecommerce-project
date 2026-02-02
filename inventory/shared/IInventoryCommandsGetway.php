<?php
namespace inventory\shared;

interface IInventoryCommandsGetway
{
    public function updateInventoryForProduct($productId, array $data);
    public function createInventoryForProduct($productId, array $data);
}
