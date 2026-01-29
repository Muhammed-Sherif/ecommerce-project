<?php
namespace referment\application\commands;

class DeleteReferment
{
    public static function execute($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Referment id is required');
        }
        return $id;
    }
}
