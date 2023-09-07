<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;


$config = new SystemConfiguration(1072);

$config->setAllowedGrowthPercentage(20);

$config->setMinSize(12);

$config->setHasPlanets(false);

$config->setHasMoons(false);

$config->setHasAsteroids(true);

$config->setMaxAsteroids(5);


return $config;
