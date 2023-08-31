<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface SizeGeneratorInterface
{
    /**
     * Generates the map data and initializes the size.
     */
    public function generate(SystemConfigurationInterface $config): SystemMapDataInterface;
}
