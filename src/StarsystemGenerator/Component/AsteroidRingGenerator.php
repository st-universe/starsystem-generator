<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class AsteroidRingGenerator implements AsteroidRingGeneratorInterface
{
    public function generate(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void {
    }
}
