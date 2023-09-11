<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Config;

use RuntimeException;
use Stu\StarsystemGenerator\Lib\StuRandom;

final class PlanetRadius
{
    private const PLANET_RADIUS_PERCENTAGE = [
        201    =>     [30,    70],
        203    =>     [25,    60],
        205    =>     [20,    50],
        207    =>     [10,    40],
        209    =>     [10,    40],
        211    =>     [30,    80],
        213    =>     [10,    60],
        215    =>     [50,    90],
        216    =>     [40,    85],
        217    =>     [10,    40],
        219    =>     [20,    70],
        221    =>     [40,    90],
        223    =>     [60,    90],
        231    =>     [50,    90],
        261    =>     [70,    90],
        262    =>     [70,    90],
        263    =>     [70,    90],
        301    =>     [30,    70],
        303    =>     [25,    60],
        305    =>     [20,    50],
        311    =>     [30,    80],
        313    =>     [10,    60],
        315    =>     [50,    90],
        317    =>     [10,    40],
        331    =>     [50,    90],
        361    =>     [70,    90],
        362    =>     [70,    90],
        363    =>     [70,    90]
    ];

    public static function getRandomPlanetRadiusPercentage(int $planetType, StuRandom $stuRandom, bool $isDwarfPlanet = false): int
    {
        $type = $isDwarfPlanet ? $planetType - 200 : $planetType;

        if (!array_key_exists($type, self::PLANET_RADIUS_PERCENTAGE)) {
            throw new RuntimeException(sprintf('planet type %d is unknown', $type));
        }

        [$min, $max] = self::PLANET_RADIUS_PERCENTAGE[$type];

        return $stuRandom->rand($min, $max, true);
    }
}
