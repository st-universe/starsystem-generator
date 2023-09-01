<?php

namespace Stu\StarsystemGenerator\Config;

interface PlanetMoonProbabilitiesInterface
{
    /**
     * @param array<int, int> $triedPlanetFieldIds
     * @param array<int, int> $customProbabilities
     */
    public function pickRandomFieldId(
        array $triedPlanetFieldIds,
        array $customProbabilities = null,
        bool $isMoon = false
    ): int;
}
