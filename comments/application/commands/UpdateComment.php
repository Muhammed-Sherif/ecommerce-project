<?php
namespace comments\application\commands;

class UpdateComment
{
    public static function execute(array $existing, array $data): array
    {
        $updated = $existing;

        if (array_key_exists('content', $data)) {
            if (trim((string) $data['content']) === '') {
                throw new \InvalidArgumentException('Comment content cannot be empty');
            }
            $updated['content'] = trim((string) $data['content']);
        }

        if (array_key_exists('rating', $data)) {
            $rating = $data['rating'] === null ? null : (int) $data['rating'];
            if ($rating !== null && ($rating < 1 || $rating > 5)) {
                throw new \InvalidArgumentException('Rating must be between 1 and 5');
            }
            $updated['rating'] = $rating;
        }

        if (isset($data['status'])) {
            $updated['status'] = $data['status'];
        }

        return $updated;
    }
}
