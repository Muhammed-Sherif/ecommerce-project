<?php
namespace comments\application\queries;

class GetAllComments
{
    public static function execute(iterable $comments): array
    {
        if ($comments instanceof \Illuminate\Support\Collection) {
            return $comments->toArray();
        }

        if (is_array($comments)) {
            return $comments;
        }

        return iterator_to_array($comments);
    }
}
