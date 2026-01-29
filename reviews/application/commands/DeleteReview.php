<?php
namespace reviews\application\commands;

class DeleteReview
{
    public static function execute(array $data): array
    {
        if (empty($data['review_id'])) {
            throw new \InvalidArgumentException('Review ID is required');
        }

        return [
            'review_id' => $data['review_id'],
        ];
    }
}
