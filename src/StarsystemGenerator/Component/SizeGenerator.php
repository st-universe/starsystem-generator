<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\SystemMapData;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class SizeGenerator implements SizeGeneratorInterface
{
    private StuRandom $stuRandom;

    public function __construct(StuRandom $stuRandom)
    {
        $this->stuRandom = $stuRandom;
    }

    public function generate(SystemConfigurationInterface $config, bool $hasTwoMassCenters): SystemMapDataInterface
    {
        $size = $this->calculateSize($config, $hasTwoMassCenters);

        return new SystemMapData($size, $size);
    }

    private function calculateSize(SystemConfigurationInterface $config, bool $hasTwoMassCenters): int
    {
        $allowedGrowthPercentage = $config->getAllowedGrowthPercentage();

        $minIncrease = $hasTwoMassCenters ? (int)($allowedGrowthPercentage / 2) : 0;

        $size = (int)($config->getMinSize() * (1 + $this->stuRandom->rand($minIncrease, $allowedGrowthPercentage, true) / 100));
        echo "size: " . $size;

        return $size;
    }
}
