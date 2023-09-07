<?php

namespace Stu\StarsystemGenerator\Lib;

interface PlanetDisplayInterface
{
    public function getIdentifier(): ?string;

    /**
     * @return array<PointInterface>
     */
    public function getPoints(): array;

    public function getRandomPoint(StuRandom $stuRandom): PointInterface;

    public function getFirstPoint(): PointInterface;

    public function getLastPoint(): PointInterface;

    public function getMoonIdentifier(): string;
}
