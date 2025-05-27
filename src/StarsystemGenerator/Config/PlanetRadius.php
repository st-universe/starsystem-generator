<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Config;

use RuntimeException;
use Stu\StarsystemGenerator\Lib\StuRandom;

final class PlanetRadius
{
    public const PLANET_RADIUS_PERCENTAGE = [
        201    =>     [30,    80],
        203    =>     [25,    70],
        205    =>     [20,    65],
        207    =>     [10,    60],
        209    =>     [10,    60],
        211    =>     [30,    80],
        213    =>     [10,    75],
        215    =>     [50,    100],
        216    =>     [40,    85],
        217    =>     [10,    60],
        219    =>     [20,    70],
        221    =>     [40,    100],
        223    =>     [60,    100],
        231    =>     [50,    100],
        261    =>     [70,    100],
        262    =>     [70,   100],
        263    =>     [70,    100],
        301    =>     [30,    70],
        303    =>     [25,    70],
        305    =>     [20,    60],
        311    =>     [30,    90],
        313    =>     [10,    70],
        315    =>     [50,    100],
        317    =>     [10,    60],
        331    =>     [50,    100],
        361    =>     [70,    100],
        362    =>     [70,    100],
        363    =>     [70,    100]
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
