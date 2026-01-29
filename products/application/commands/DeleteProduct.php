<?php
namespace products\application\commands;

class DeleteProduct
{
    public static function execute($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Product id is required to delete');
        }
        return $id;
    }
}
