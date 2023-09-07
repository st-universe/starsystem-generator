<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1042);

$config->setAllowedGrowthPercentage(30);

$config->setMinSize(24);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(12);

$config->setMaxMoons(30);

$config->setMaxAsteroids(30);



return $config;
