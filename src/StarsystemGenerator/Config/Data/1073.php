<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration();

$config->setAllowedGrowthPercentage(300);

$config->setMinSize(17);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(4);

$config->setMaxMoons(5);

$config->setMaxAsteroids(10);


return $config;
