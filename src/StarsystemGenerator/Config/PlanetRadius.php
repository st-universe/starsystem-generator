<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Config;

use RuntimeException;

final class PlanetRadius
{
    private const PLANET_RADIUS_PERCENTAGE = [
        201    =>     [30,    60],
        203    =>     [25,    50],
        205    =>     [20,    45],
        207    =>     [10,    30],
        209    =>     [10,    30],
        211    =>     [30,    60],
        213    =>     [20,    50],
        215    =>     [50,    90],
        216    =>     [40,    75],
        217    =>     [10,    30],
        219    =>     [40,    70],
        221    =>     [50,    80],
        223    =>     [60,    90],
        231    =>     [50,    90],
        261    =>     [70,    90],
        262    =>     [70,    90],
        263    =>     [70,    90],
        301    =>     [30,    60],
        303    =>     [25,    50],
        305    =>     [20,    45],
        311    =>     [30,    60],
        313    =>     [20,    50],
        315    =>     [50,    90],
        317    =>     [10,    30],
        331    =>     [50,    90],
        361    =>     [70,    90],
        362    =>     [70,    90],
        363    =>     [70,    90]
    ];

    public static function getRandomPlanetRadiusPercentage(int $planetType): int
    {
        if (!array_key_exists($planetType, self::PLANET_RADIUS_PERCENTAGE)) {
            throw new RuntimeException(sprintf('planet type %d is unknown', $planetType));
        }

        [$min, $max] = self::PLANET_RADIUS_PERCENTAGE[$planetType];

        return random_int($min, $max);
    }
}
