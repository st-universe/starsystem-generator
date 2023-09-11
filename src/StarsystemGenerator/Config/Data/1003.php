<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1003);

$config->setAllowedGrowthPercentage(50);

$config->setMinSize(24);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(16);

$config->setMaxMoons(50);

$config->setMaxAsteroids(195);



return $config;
