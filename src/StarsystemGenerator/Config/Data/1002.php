<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration();

$config->setAllowedGrowthPercentage(300);

return $config;
