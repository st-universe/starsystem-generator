<?php

namespace Stu\StarsystemGenerator;

use Stu\StarsystemGenerator\Lib\PointInterface;

interface BlockedFieldDataInterface
{
    public function getBlockType(int $index): int;

    public function blockField(
        PointInterface $point,
        bool $blockSurrounding,
        ?int $fieldType,
        int $blockType
    ): void;

    /**
     * @return array<int, int>
     */
    public function getData(): array;
}
