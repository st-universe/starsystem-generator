<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;

$config = new SystemConfiguration();

$config->setAllowedGrowthPercentage(300);

$config->setMinSize(7);

$config->setHasPlanets(false);

$config->setHasMoons(false);

$config->setHasAsteroids(false);



return $config;
