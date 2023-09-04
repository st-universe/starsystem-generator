<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface PlanetPlacementInterface
{
    /**
     * @return array<int, array{0:int, 1:int}>
     */
    public function placePlanet(int &$planetAmount, SystemMapDataInterface $mapData, SystemConfigurationInterface $config): array;
}
