<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1022);

$config->setAllowedGrowthPercentage(25);

$config->setMinSize(22);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(14);

$config->setMaxMoons(40);

$config->setMaxAsteroids(60);



return $config;
