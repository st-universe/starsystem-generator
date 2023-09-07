<?php

namespace Stu\StarsystemGenerator;

use Stu\StarsystemGenerator\Lib\FieldInterface;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;

interface SystemMapDataInterface
{
    public function getWidth(): int;

    public function getHeight(): int;

    public function setField(
        FieldInterface $field,
        int $maxAllowedBlock = 0
    ): SystemMapDataInterface;

    public function blockField(
        PointInterface $point,
        bool $blockSurrounding,
        ?int $fieldType,
        int $blockType
    ): void;

    /** @return array<int, PointInterface> */
    public function getAsteroidRing(int $radiusPercentage, int $variance): array;

    public function getRandomPlanetAmount(StuRandom $stuRandom): int;

    public function getRandomMoonAmount(StuRandom $stuRandom): int;

    /** @return null|array<PointInterface> */
    public function getPlanetDisplay(int $radiusPercentage, int $moonRange): ?array;

    /** @return array<int, int> */
    public function getFieldData(): array;

    public function toString(bool $doPrint = false, bool $showBlocked = false): string;
}
