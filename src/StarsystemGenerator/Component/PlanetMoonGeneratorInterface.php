<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface PlanetMoonGeneratorInterface
{
    /**
     * Adds planet and moon fields to the map data input.
     */
    public function generate(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void;
}
