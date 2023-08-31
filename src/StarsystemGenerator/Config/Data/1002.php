<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration();

$config->setAllowedGrowthPercentage(100);

return $config;
