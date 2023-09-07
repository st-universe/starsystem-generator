<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Config;

use RuntimeException;
use Stu\StarsystemGenerator\Exception\NoSuitablePlanetTypeFoundException;

final class PlanetMoonProbabilities implements PlanetMoonProbabilitiesInterface
{
    private const PLANET_PROBABILITIES = [
        231    => 175,
        215    => 160,
        213    => 150,
        211    => 148,
        201    => 132,
        203    => 131,
        217    => 124,
        205    => 123,
        221    => 112,
        223    => 101,
        209    => 84,
        219    => 81,
        207    => 78,
        361    => 45,
        262    => 34,
        216    => 33,
        362    => 33,
        263    => 29,
        363    => 28,
        261    => 22,
        301    => 18
    ];

    private const MOON_PROBABILITIES = [
        431 => 721,
        415 => 300,
        411 => 283,
        407 => 197,
        417 => 169,
        413 => 164,
        423 => 155,
        416 => 88,
        421 => 82,
        419 => 79,
        403 => 77,
        405 => 61,
        401 => 60,
        409 => 49
    ];

    public function pickRandomFieldId(
        array $triedPlanetFieldIds,
        array $customProbabilities,
        array $probabilityBlacklist,
        bool $isMoon = false
    ): int {
        if (!empty($customProbabilities)) {
            $probabilities = $customProbabilities;
        } else {
            $probabilities = $isMoon ? static::MOON_PROBABILITIES : static::PLANET_PROBABILITIES;
        }

        $probabilities = array_filter(
            $probabilities,
            fn ($key) => !in_array($key, $triedPlanetFieldIds)
                && !in_array($key, $probabilityBlacklist),
            ARRAY_FILTER_USE_KEY
        );

        $totalProbability = array_sum($probabilities);

        if ($totalProbability === 0) {
            throw new NoSuitablePlanetTypeFoundException();
        }

        $randomNumber = random_int(1, $totalProbability);
        $cumulativeProbability = 0;

        foreach ($probabilities as $fieldId => $value) {
            $cumulativeProbability += $value;
            if ($randomNumber <= $cumulativeProbability) {
                return $fieldId;
            }
        }

        throw new RuntimeException('this should not happen');
    }
}
