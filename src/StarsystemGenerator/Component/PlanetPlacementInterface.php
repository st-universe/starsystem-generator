<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Lib\PlanetDisplayInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface PlanetPlacementInterface
{
    public function placePlanet(
        int &$planetAmount,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): PlanetDisplayInterface;
}
