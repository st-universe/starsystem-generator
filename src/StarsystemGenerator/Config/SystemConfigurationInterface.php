<?php

namespace Stu\StarsystemGenerator\Config;

interface SystemConfigurationInterface
{
    public function getMinSize(): int;

    public function setMinSize(int $minSize): SystemConfigurationInterface;

    public function setAllowedGrowthPercentage(int $allowedGrowthPercentage): SystemConfigurationInterface;

    public function setHasPlanets(bool $hasPlanets): SystemConfigurationInterface;

    public function setHasMoons(bool $hasMoons): SystemConfigurationInterface;

    public function setHasAsteroids(bool $hasAsteroids): SystemConfigurationInterface;

    public function addPropability(int $type, int $percentage, int $probabilityType): SystemConfigurationInterface;

    public function setMaxPlanets(int $maxPlanets): SystemConfigurationInterface;

    public function setMaxMoons(int $maxMoons): SystemConfigurationInterface;

    public function setMaxAsteroids(int $maxAsteroids): SystemConfigurationInterface;
}
