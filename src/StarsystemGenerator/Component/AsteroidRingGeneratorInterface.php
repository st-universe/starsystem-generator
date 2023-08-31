<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface AsteroidRingGeneratorInterface
{
    /**
     * Adds asteroid ring fields to the map data input.
     */
    public function generate(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void;
}
