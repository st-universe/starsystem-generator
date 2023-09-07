<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1073);

$config->setAllowedGrowthPercentage(20);

$config->setMinSize(17);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(4);

$config->setMaxMoons(5);

$config->setMaxAsteroids(10);


return $config;
