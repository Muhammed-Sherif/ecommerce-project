<?php
namespace comments\application\commands;

class CreateComment
{
    public static function execute(array $comment): array
    {
        if (empty($comment['product_id'])) {
            throw new \InvalidArgumentException('Product id is required');
        }
        if (empty($comment['user_id'])) {
            throw new \InvalidArgumentException('User id is required');
        }
        if (empty($comment['content'])) {
            throw new \InvalidArgumentException('Comment content is required');
        }

        $rating = isset($comment['rating']) ? (int) $comment['rating'] : null;
        if ($rating !== null && ($rating < 1 || $rating > 5)) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }

        return [
            'product_id' => $comment['product_id'],
            'user_id' => $comment['user_id'],
            'content' => trim($comment['content']),
            'rating' => $rating,
            'status' => $comment['status'] ?? 'active',
        ];
    }
}
