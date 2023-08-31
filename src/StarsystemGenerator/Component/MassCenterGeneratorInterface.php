<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface MassCenterGeneratorInterface
{
    /**
     * Adds the mass center fields to map data input.
     * 
     * @param array<int, int> $firstMassCenterFields
     * @param array<int, int>|null $secondMassCenterFields
     */
    public function generate(
        array $firstMassCenterFields,
        ?array $secondMassCenterFields,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void;
}
