<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Config;

final class PlanetMoonRange
{
    private const MOON_RANGES = [
        217 => 2,
        221 => 2,
        223  => 2,
        261  => 2,
        262  => 2,
        263  => 2
    ];

    private const DEFAULT_MOON_RANGE = 1;
    private const MOON_RANGE_FOR_RING_PLANETS = 2;

    public static function getPlanetMoonRange(int $planetType): int
    {
        if (array_key_exists($planetType, self::MOON_RANGES)) {
            return self::MOON_RANGES[$planetType];
        }

        if ((int)round($planetType / 100) === 3) {
            return self::MOON_RANGE_FOR_RING_PLANETS;
        }

        return self::DEFAULT_MOON_RANGE;
    }
}
