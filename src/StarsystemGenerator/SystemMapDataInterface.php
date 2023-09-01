<?php

namespace Stu\StarsystemGenerator;

use Stu\StarsystemGenerator\Lib\StuRandom;

interface SystemMapDataInterface
{
    public function getWidth(): int;

    public function getHeight(): int;

    public function setFieldId(
        int $x,
        int $y,
        int $fieldId,
        int $fieldType,
        bool $allowSoftBlock = false
    ): SystemMapDataInterface;

    public function blockField(
        int $x,
        int $y,
        bool $blockSurrounding,
        ?int $fieldType,
        int $blockType
    ): void;

    /** @return array<int, array{0: int, 1: int}> */
    public function getAsteroidRing(int $radiusPercentage): array;

    public function getRandomPlanetAmount(StuRandom $stuRandom): int;

    public function getRandomMoonAmount(StuRandom $stuRandom): int;

    /** @return null|array<int, array{0: int, 1: int}> */
    public function getPlanetDisplay(int $radiusPercentage, int $moonRange): ?array;

    /** @return array<int, int> */
    public function getFieldData(): array;

    public function toString(bool $doPrint = false, bool $showBlocked = false): string;
}
