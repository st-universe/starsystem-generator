<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface PlanetPlacementInterface
{
    /**
     * @return array<int, PointInterface>
     */
    public function placePlanet(
        int &$planetAmount,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): array;
}
