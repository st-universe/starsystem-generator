<?php

namespace Stu\StarsystemGenerator\Config;

interface PlanetMoonProbabilitiesInterface
{
    /**
     * @param array<int> $triedPlanetFieldIds
     * @param array<int> $customProbabilities
     * @param array<int> $probabilityBlacklist
     */
    public function pickRandomFieldId(
        array $triedPlanetFieldIds,
        array $customProbabilities,
        array $probabilityBlacklist,
        bool $isMoon = false
    ): int;
}
