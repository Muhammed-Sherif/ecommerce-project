<?php
namespace comments\application\commands;

class DeleteComment
{
    public static function execute($id)
    {
        if (!$id) {
            throw new \InvalidArgumentException('Comment id is required to delete');
        }
        return $id;
    }
}
