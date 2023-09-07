<?php

use Stu\StarsystemGenerator\Config\SystemConfiguration;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;

$config = new SystemConfiguration(1071);

$config->setAllowedGrowthPercentage(20);

$config->setMinSize(12);

$config->setHasPlanets(true);

$config->setHasMoons(true);

$config->setHasAsteroids(true);

$config->setMaxPlanets(2);

$config->setMaxMoons(2);

$config->setMaxAsteroids(5);

$config->setPropabilityBlacklist(FieldTypeEnum::MOON, [401, 402, 403, 404, 405, 407, 409, 411, 413, 415, 416, 417, 419, 431]);

$config->setPropabilityBlacklist(FieldTypeEnum::PLANET, [201, 203, 205, 207, 209, 211, 213, 217, 215, 216, 219, 231, 301, 303, 305, 311, 313, 315, 317, 331,]);

return $config;
