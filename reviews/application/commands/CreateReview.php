<?php
namespace reviews\application\commands;

class CreateReview
{
    public static function execute(array $data): array
    {
        // Validate product ID
        if (empty($data['product_id'])) {
            throw new \InvalidArgumentException('Product ID is required');
        }

        // Validate user ID
        if (empty($data['user_id'])) {
            throw new \InvalidArgumentException('User ID is required');
        }

        // Validate rating
        if (!isset($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }

        // Validate comment
        if (empty($data['comment'])) {
            throw new \InvalidArgumentException('Review comment is required');
        }

        return [
            'product_id' => $data['product_id'],
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'] ?? 'Anonymous',
            'rating' => (int) $data['rating'],
            'title' => trim((string) ($data['title'] ?? '')),
            'comment' => trim($data['comment']),
        ];
    }
}
