<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1035);

$config->setAllowedGrowthPercentage(25);

$config->setMinSize(20);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(12);

$config->setMaxMoons(35);

$config->setMaxAsteroids(50);



return $config;
