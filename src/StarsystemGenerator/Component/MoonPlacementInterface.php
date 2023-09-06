<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface MoonPlacementInterface
{
    /**
     * @param null|array<int, PointInterface> $planetDisplay
     */
    public function placeMoon(
        int &$moonAmount,
        ?array $planetDisplay,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void;
}
