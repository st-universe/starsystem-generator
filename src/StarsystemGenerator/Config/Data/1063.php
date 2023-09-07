<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1063);

$config->setAllowedGrowthPercentage(20);

$config->setMinSize(7);

$config->setHasPlanets(false);

$config->setHasMoons(false);

$config->setHasAsteroids(false);



return $config;
