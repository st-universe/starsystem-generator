<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Lib\PlanetDisplayInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface MoonPlacementInterface
{
    public function placeMoon(
        int &$moonAmount,
        int &$planetAmount,
        ?PlanetDisplayInterface $planetDisplay,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void;
}
