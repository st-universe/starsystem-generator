<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration();

$config->setAllowedGrowthPercentage(300);

$config->setMinSize(12);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(2);

$config->setMaxMoons(2);

$config->setMaxAsteroids(5);


return $config;
