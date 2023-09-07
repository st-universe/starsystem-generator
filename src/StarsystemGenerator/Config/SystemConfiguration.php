<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Config;

use InvalidArgumentException;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;

//TODO unit tests
final class SystemConfiguration implements SystemConfigurationInterface
{
    private int $systemType;

    // size
    private int $minSize = 7;
    private int $allowedGrowthPercentage = 0;

    // objects
    private bool $hasPlanets = true;
    private bool $hasMoons = true;
    private bool $hasAsteroids = false;

    // object probabilities
    /** @var array<int, int> */
    private array $planetProbabilities = [];
    /** @var array<int, int> */
    private array $moonProbabilities = [];
    /** @var array<int, int> */
    private array $asteroidProbabilities = [];

    // probability blacklists
    /** @var array<int, array<int>> */
    private array $probabilityBlacklists = [];

    private int $maxPlanets = PHP_INT_MAX;
    private int $maxMoons = PHP_INT_MAX;
    private int $maxAsteroids = PHP_INT_MAX;

    private int $massCenterDistanceHorizontal = 2;
    private int $massCenterDistanceVertical = 2;

    public function __construct(int $systemType)
    {
        $this->systemType = $systemType;
    }

    public function getSystemType(): int
    {
        return $this->systemType;
    }

    public function getMinSize(): int
    {
        return $this->minSize;
    }

    public function setMinSize(int $minSize): SystemConfigurationInterface
    {
        $this->minSize = $minSize;

        return $this;
    }

    public function getAllowedGrowthPercentage(): int
    {
        return $this->allowedGrowthPercentage;
    }

    public function setAllowedGrowthPercentage(int $allowedGrowthPercentage): SystemConfigurationInterface
    {
        $this->allowedGrowthPercentage = $allowedGrowthPercentage;

        return $this;
    }

    public function hasPlanets(): bool
    {
        return $this->hasPlanets;
    }

    public function setHasPlanets(bool $hasPlanets): SystemConfigurationInterface
    {
        $this->hasPlanets = $hasPlanets;

        return $this;
    }

    public function hasMoons(): bool
    {
        return $this->hasMoons;
    }

    public function setHasMoons(bool $hasMoons): SystemConfigurationInterface
    {
        $this->hasMoons = $hasMoons;

        return $this;
    }

    public function hasAsteroids(): bool
    {
        return $this->hasAsteroids;
    }

    public function setHasAsteroids(bool $hasAsteroids): SystemConfigurationInterface
    {
        $this->hasAsteroids = $hasAsteroids;

        return $this;
    }

    public function addPropability(int $type, int $percentage, int $probabilityType): SystemConfigurationInterface
    {
        $this->getProbabilities($probabilityType)[$type] = $percentage;

        return $this;
    }

    public function getProbabilities(int $type): array
    {
        switch ($type) {
            case FieldTypeEnum::PLANET:
                return $this->planetProbabilities;
            case FieldTypeEnum::MOON:
                return $this->moonProbabilities;
            case FieldTypeEnum::ASTEROID:
                return $this->asteroidProbabilities;
        }

        throw new InvalidArgumentException(sprintf('probabilityType %d is not supported', $type));
    }

    public function getPropabilityBlacklist(int $type): array
    {
        if (!array_key_exists($type, $this->probabilityBlacklists)) {
            return [];
        }

        return $this->probabilityBlacklists[$type];
    }

    public function setPropabilityBlacklist(int $type, array $blacklistTypes): SystemConfigurationInterface
    {
        $this->probabilityBlacklists[$type] = $blacklistTypes;

        return $this;
    }

    public function getMaxPlanets(): int
    {
        return $this->maxPlanets;
    }

    public function setMaxPlanets(int $maxPlanets): SystemConfigurationInterface
    {
        $this->maxPlanets = $maxPlanets;

        return $this;
    }

    public function getMaxMoons(): int
    {
        return $this->maxMoons;
    }

    public function setMaxMoons(int $maxMoons): SystemConfigurationInterface
    {
        $this->maxMoons = $maxMoons;

        return $this;
    }

    public function getMaxAsteroids(): int
    {
        return $this->maxAsteroids;
    }

    public function setMaxAsteroids(int $maxAsteroids): SystemConfigurationInterface
    {
        $this->maxAsteroids = $maxAsteroids;

        return $this;
    }

    public function getMassCenterDistanceHorizontal(): int
    {
        return $this->massCenterDistanceHorizontal;
    }

    public function getMassCenterDistanceVertical(): int
    {
        return $this->massCenterDistanceVertical;
    }
}
