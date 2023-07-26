<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

interface SizeGeneratorInterface
{
    public function generate(SystemConfigurationInterface $config): SystemMapDataInterface;
}
