<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;

$config = new SystemConfiguration(1070);

$config->setAllowedGrowthPercentage(20);

$config->setMinSize(15);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(4);

$config->setMaxMoons(3);

$config->setMaxAsteroids(12);

$config->setPropabilityBlacklist(FieldTypeEnum::MOON, [401, 402, 403, 404, 405, 407, 409, 411, 413, 419]);

$config->setPropabilityBlacklist(FieldTypeEnum::PLANET, [201, 203, 205, 207, 209, 211, 213, 219, 301, 303, 305, 311, 313]);

return $config;
