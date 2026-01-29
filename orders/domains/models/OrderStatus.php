<?php
namespace orders\domains\models;

class OrderStatus
{
    public const PENDING = 'pending';
    public const PAID = 'paid';
    public const CONFIRMED = 'confirmed';
    public const SHIPPED = 'shipped';
    public const DELIVERED = 'delivered';
    public const CANCELLED = 'cancelled';

    private static $validStatuses = [
        self::PENDING,
        self::PAID,
        self::CONFIRMED,
        self::SHIPPED,
        self::DELIVERED,
        self::CANCELLED,
    ];

    private static $validTransitions = [
        self::PENDING => [self::PAID, self::CONFIRMED, self::CANCELLED],
        self::PAID => [self::CONFIRMED, self::CANCELLED],
        self::CONFIRMED => [self::SHIPPED, self::CANCELLED],
        self::SHIPPED => [self::DELIVERED],
        self::DELIVERED => [],
        self::CANCELLED => [],
    ];

    public static function isValid(string $status): bool
    {
        return in_array($status, self::$validStatuses);
    }

    public static function canTransition(string $from, string $to): bool
    {
        if (!self::isValid($from) || !self::isValid($to)) {
            return false;
        }

        return in_array($to, self::$validTransitions[$from] ?? []);
    }

    public static function getValidStatuses(): array
    {
        return self::$validStatuses;
    }
}
