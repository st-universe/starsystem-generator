<?php

namespace Stu\StarsystemGenerator\Config;

interface PlanetMoonProbabilitiesInterface
{
    /**
     * @param array<int, int> $customProbabilities
     */
    public function pickRandomFieldId(array $customProbabilities = null, bool $isMoon = false): int;
}
