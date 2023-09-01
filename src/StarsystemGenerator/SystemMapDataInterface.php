<?php

namespace Stu\StarsystemGenerator;

interface SystemMapDataInterface
{
    public function getWidth(): int;

    public function getHeight(): int;

    public function getFieldAmount(): int;

    public function setFieldId(
        int $x,
        int $y,
        int $fieldId,
        int $fieldType
    ): SystemMapDataInterface;

    /** @return array<int, array{0: int, 1: int}> */
    public function getAsteroidRing(int $radiusPercentage): array;

    /** @return null|array<int, array{0: int, 1: int}> */
    public function getPlanetDisplay(int $radiusPercentage, int $moonRange): ?array;

    /** @return array<int, int> */
    public function getFieldData(): array;

    public function toString(bool $doPrint = false, bool $showBlocked = false): string;
}
