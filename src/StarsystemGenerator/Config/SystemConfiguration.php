<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Config;

use InvalidArgumentException;
use Stu\StarsystemGenerator\Enum\ProbabilityTypeEnum;

final class SystemConfiguration implements SystemConfigurationInterface
{
    // size
    private int $minSize = 7;
    private int $allowedGrowthPercentage = 0;

    // objects
    private bool $hasPlanets = false;
    private bool $hasMoons = false;
    private bool $hasAsteroids = false;

    // object probabilities
    /** @var array<int, int> */
    private array $planetProbabilities = [];
    /** @var array<int, int> */
    private array $moonProbabilities = [];
    /** @var array<int, int> */
    private array $asteroidProbabilities = [];

    private int $maxPlanets = PHP_INT_MAX;
    private int $maxMoons = PHP_INT_MAX;
    private int $maxAsteroids = PHP_INT_MAX;

    public function getMinSize(): int
    {
        return $this->minSize;
    }

    public function setMinSize(int $minSize): SystemConfigurationInterface
    {
        $this->minSize = $minSize;

        return $this;
    }

    public function setAllowedGrowthPercentage(int $allowedGrowthPercentage): SystemConfigurationInterface
    {
        $this->allowedGrowthPercentage = $allowedGrowthPercentage;

        return $this;
    }

    public function setHasPlanets(bool $hasPlanets): SystemConfigurationInterface
    {
        $this->hasPlanets = $hasPlanets;

        return $this;
    }

    public function setHasMoons(bool $hasMoons): SystemConfigurationInterface
    {
        $this->hasMoons = $hasMoons;

        return $this;
    }

    public function setHasAsteroids(bool $hasAsteroids): SystemConfigurationInterface
    {
        $this->hasAsteroids = $hasAsteroids;

        return $this;
    }

    public function addPropability(int $type, int $percentage, int $probabilityType): SystemConfigurationInterface
    {
        switch ($probabilityType) {
            case ProbabilityTypeEnum::PLANET:
                $this->planetProbabilities[$type] = $percentage;
                break;
            case ProbabilityTypeEnum::MOON:
                $this->moonProbabilities[$type] = $percentage;
                break;
            case ProbabilityTypeEnum::ASTEROID:
                $this->asteroidProbabilities[$type] = $percentage;
                break;
            default:
                throw new InvalidArgumentException(sprintf('probabilityType %d is not supported', $probabilityType));
        }

        return $this;
    }

    public function setMaxPlanets(int $maxPlanets): SystemConfigurationInterface
    {
        $this->maxPlanets = $maxPlanets;

        return $this;
    }

    public function setMaxMoons(int $maxMoons): SystemConfigurationInterface
    {
        $this->maxMoons = $maxMoons;

        return $this;
    }

    public function setMaxAsteroids(int $maxAsteroids): SystemConfigurationInterface
    {
        $this->maxAsteroids = $maxAsteroids;

        return $this;
    }
}
