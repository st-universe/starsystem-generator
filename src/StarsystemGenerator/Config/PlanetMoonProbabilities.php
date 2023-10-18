<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Config;

use RuntimeException;
use Stu\StarsystemGenerator\Exception\NoSuitablePlanetTypeFoundException;

final class PlanetMoonProbabilities implements PlanetMoonProbabilitiesInterface
{
    private const PLANET_PROBABILITIES = [
        231 => 100,
        215 => 92,
        213 => 86,
        219 => 85,
        211 => 84,
        201 => 74,
        203 => 73,
        217 => 70,
        205 => 69,
        221 => 63,
        223 => 57,
        209 => 47,
        207 => 43,
        361 => 25,
        262 => 19,
        216 => 18,
        362 => 18,
        263 => 15,
        363 => 15,
        317 => 14,
        331 => 13,
        313 => 12,
        315 => 12,
        311 => 12,
        261 => 11,
        305 => 9,
        303 => 9,
        301 => 9,
    ];


    private const MOON_PROBABILITIES = [
        431 => 100,
        415 => 41,
        419 => 40,
        413 => 40,
        411 => 39,
        403 => 25,
        405 => 22,
        401 => 21,
        417 => 15,
        407 => 13,
        416 => 12,
        421 => 11,
        423 => 9,
        409 => 6
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
