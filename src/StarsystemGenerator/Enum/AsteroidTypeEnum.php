<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Enum;

final class AsteroidTypeEnum
{
    // ASTEROID TYPES
    public const ASTEROID_TYPE_NORMAL = 701;  // 701, 702, 703
    public const ASTEROID_TYPE_YELLOW = 704;  // 704, 705, 706
    public const ASTEROID_TYPE_BROWN = 707;   // 707, 708, 709
    public const ASTEROID_TYPE_ICE = 716;     // 716, 717, 718

    public const ASTEROID_TYPES = [
        self::ASTEROID_TYPE_NORMAL,
        self::ASTEROID_TYPE_ICE,
        self::ASTEROID_TYPE_BROWN,
        self::ASTEROID_TYPE_YELLOW
    ];

    // SUB CATEGORIES
    public const ASTEROID_CATEGORY_THIN = 0;
    public const ASTEROID_CATEGORY_MEDIUM = 1;
    public const ASTEROID_CATEGORY_HIGH = 2;

    public const ASTEROID_CATEGORIES = [
        self::ASTEROID_CATEGORY_THIN,
        self::ASTEROID_CATEGORY_MEDIUM,
        self::ASTEROID_CATEGORY_HIGH
    ];

    public static function getFieldId(int $type, int $category): int
    {
        return $type + $category;
    }
}
