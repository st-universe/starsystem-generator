<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1020);

$config->setAllowedGrowthPercentage(52);

$config->setMinSize(26);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(18);

$config->setMaxMoons(60);

$config->setMaxAsteroids(90);



return $config;
