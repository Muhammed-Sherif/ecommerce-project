<?php
namespace reviews\application\commands;

use reviews\domains\models\ReviewStatus;

class ApproveReview
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
