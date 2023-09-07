<?php

namespace Stu\StarsystemGenerator\Lib;

use RuntimeException;

class PlanetDisplay implements PlanetDisplayInterface
{
    /** @var array<int, PointInterface> */
    private array $points;
    private ?string $identifier;

    private int $moonIndex = 0;

    /**
     * @param array<int, PointInterface> $points
     */
    public function __construct(array $points, ?string $identifier)
    {
        $this->points = $points;
        $this->identifier = $identifier;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getPoints(): array
    {
        return $this->points;
    }

    public function getRandomPoint(StuRandom $stuRandom): PointInterface
    {
        return $this->points[$stuRandom->rand(0, count($this->points) - 1)];
    }

    public function getFirstPoint(): PointInterface
    {
        $firstPoint = current($this->points);
        if ($firstPoint === false) {
            throw new RuntimeException('this should not happen');
        }

        return $firstPoint;
    }

    public function getLastPoint(): PointInterface
    {
        $lastPoint = end($this->points);
        if ($lastPoint === false) {
            throw new RuntimeException('this should not happen');
        }
        return $lastPoint;
    }

    public function getMoonIdentifier(): string
    {
        $result = sprintf('%s%s', $this->getIdentifier(), chr(97 + $this->moonIndex));
        $this->moonIndex++;

        return $result;
    }
}
