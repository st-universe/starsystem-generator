<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\SystemMapData;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class SizeGenerator implements SizeGeneratorInterface
{
    public function generate(SystemConfigurationInterface $config): SystemMapDataInterface
    {
        $size = $this->calculateSize($config);

        return new SystemMapData($size, $size);
    }

    private function calculateSize(SystemConfigurationInterface $config): int
    {
        return $config->getMinSize();
    }
}
