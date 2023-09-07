<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1069);

$config->setAllowedGrowthPercentage(20);

$config->setMinSize(15);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(6);

$config->setMaxMoons(6);

$config->setMaxAsteroids(15);


return $config;
