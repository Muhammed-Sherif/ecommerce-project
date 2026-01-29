<?php
namespace inventory\domains\models;

class StockMovementType
{
    public const IN = 'in';
    public const OUT = 'out';
    public const ADJUSTMENT = 'adjustment';
    public const RESERVED = 'reserved';
    public const RELEASED = 'released';

    private static $validTypes = [
        self::IN,
        self::OUT,
        self::ADJUSTMENT,
        self::RESERVED,
        self::RELEASED,
    ];

    public static function isValid(string $type): bool
    {
        return in_array($type, self::$validTypes);
    }

    public static function getValidTypes(): array
    {
        return self::$validTypes;
    }
}
