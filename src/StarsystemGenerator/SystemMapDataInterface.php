<?php

namespace Stu\StarsystemGenerator;

use Stu\StarsystemGenerator\Lib\FieldInterface;
use Stu\StarsystemGenerator\Lib\PlanetDisplayInterface;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;

interface SystemMapDataInterface
{
    public const OUTPUT_CATEGORY_MAP_DATA = 1;
    public const OUTPUT_CATEGORY_BLOCKED_DATA = 2;
    public const OUTPUT_CATEGORY_IDENTIFIER_DATA = 3;

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

    public function addIdentifier(PointInterface $point, string $identifier): void;

    public function getIdentifier(int $index): string;

    /** @return array<int, PointInterface> */
    public function getAsteroidRing(int $radiusPercentage, int $variance): array;

    public function getRandomPlanetAmount(StuRandom $stuRandom): int;

    public function getRandomMoonAmount(StuRandom $stuRandom): int;

    /** @return null|PlanetDisplayInterface */
    public function getPlanetDisplay(int $radiusPercentage, int $moonRange, string $identifier = null): ?PlanetDisplayInterface;

    /** @return array<int, int> */
    public function getFieldData(): array;

    public function toString(bool $doPrint = false, bool $showBlocked = false): string;

    public function printIdentifiers(): void;
}
