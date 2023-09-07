<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration(1064);

$config->setAllowedGrowthPercentage(20);

$config->setMinSize(10);

$config->setHasPlanets(false);

$config->setHasMoons(false);

$config->setHasAsteroids(true);

$config->setMaxAsteroids(10);


return $config;
