<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface PlanetRingPlacementInterface
{
    public function addPlanetRing(int $planetFieldId, PointInterface $planetLocation, SystemMapDataInterface $mapData): void;
}
