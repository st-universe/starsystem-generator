<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration();

$config->setAllowedGrowthPercentage(300);

$config->setMinSize(21);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(10);

$config->setMaxMoons(20);

$config->setMaxAsteroids(30);



return $config;
