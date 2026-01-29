<?php
namespace reviews\domains\models;

class ReviewStatus
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';

    private static $validStatuses = [
        self::PENDING,
        self::APPROVED,
        self::REJECTED,
    ];

    public static function isValid(string $status): bool
    {
        return in_array($status, self::$validStatuses);
    }

    public static function getValidStatuses(): array
    {
        return self::$validStatuses;
    }
}
