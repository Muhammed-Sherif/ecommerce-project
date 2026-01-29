<?php
namespace copons\application\commands;

class DeleteCopon
{
    public static function execute($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Copon id is required');
        }
        return $id;
    }
}
