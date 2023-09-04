<?php

namespace Stu\StarsystemGenerator\Config;

interface SystemConfigurationInterface
{
    public function getMinSize(): int;

    public function setMinSize(int $minSize): SystemConfigurationInterface;

    public function getAllowedGrowthPercentage(): int;

    public function setAllowedGrowthPercentage(int $allowedGrowthPercentage): SystemConfigurationInterface;

    public function hasPlanets(): bool;

    public function setHasPlanets(bool $hasPlanets): SystemConfigurationInterface;

    public function hasMoons(): bool;

    public function setHasMoons(bool $hasMoons): SystemConfigurationInterface;

    public function hasAsteroids(): bool;

    public function setHasAsteroids(bool $hasAsteroids): SystemConfigurationInterface;

    /** @return array<int, int> */
    public function getProbabilities(int $probabilityType): array;

    public function addPropability(int $type, int $percentage, int $probabilityType): SystemConfigurationInterface;

    /** @return array<int> */
    public function getPropabilityBlacklist(int $type): array;

    /** @param array<int> $blacklistTypes */
    public function setPropabilityBlacklist(int $type, array $blacklistTypes): SystemConfigurationInterface;

    public function getMaxPlanets(): int;

    public function setMaxPlanets(int $maxPlanets): SystemConfigurationInterface;

    public function getMaxMoons(): int;

    public function setMaxMoons(int $maxMoons): SystemConfigurationInterface;

    public function getMaxAsteroids(): int;

    public function setMaxAsteroids(int $maxAsteroids): SystemConfigurationInterface;

    public function getMassCenterDistanceHorizontal(): int;

    public function getMassCenterDistanceVertical(): int;
}
