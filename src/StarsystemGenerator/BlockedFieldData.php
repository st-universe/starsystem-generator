<?php

namespace Stu\StarsystemGenerator;

use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Lib\Point;
use Stu\StarsystemGenerator\Lib\PointInterface;

//TODO unit tests
final class BlockedFieldData implements BlockedFieldDataInterface
{
    private int $width;

    /** @var array<int, int> */
    private array $blockedFields = [];

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->blockedFields = $this->initFieldArray($width, $height);
        $this->blockOuterEdge($width, $height);
    }

    public function getBlockType(int $index): int
    {
        return $this->blockedFields[$index];
    }

    public function blockField(PointInterface $point, bool $blockSurrounding, ?int $fieldType, int $blockType): void
    {
        $index = $this->getFieldIndex($point);

        if (!array_key_exists($index, $this->blockedFields)) {
            return;
        }

        if ($this->blockedFields[$index] < $blockType) {
            $this->blockedFields[$index] = $blockType;
        }

        if ($blockSurrounding) {
            $range = $fieldType === FieldTypeEnum::MASS_CENTER ? 2 : 1;

            foreach ($this->getSurroundingPoints($point, $range) as $point) {
                $this->blockField(
                    $point,
                    false,
                    null,
                    $fieldType === FieldTypeEnum::MASS_CENTER
                        ? BlockedFieldTypeEnum::MASS_CENTER_PERIMETER_BLOCK : BlockedFieldTypeEnum::SOFT_BLOCK
                );
            }
        }
    }

    public function getData(): array
    {
        return $this->blockedFields;
    }

    /**
     * @return array<int, int>
     */
    private function initFieldArray(int $width, int $height): array
    {
        $result = [];

        for ($y = 1; $y <= $height; $y++) {
            for ($x = 1; $x <= $width; $x++) {
                $index = $x + ($y - 1) * $width;
                $result[$index] = BlockedFieldTypeEnum::NOT_BLOCKED;
            }
        }

        return $result;
    }

    private function blockOuterEdge(int $width, int $height): void
    {
        // top edge
        foreach (range(1, $width) as $x) {
            $this->blockField(new Point($x, 1), false, null, BlockedFieldTypeEnum::EDGE_BLOCK);
        }
        // bottom edge
        foreach (range(1, $width) as $x) {
            $this->blockField(new Point($x, $height), false, null, BlockedFieldTypeEnum::EDGE_BLOCK);
        }
        // left edge
        foreach (range(1, $height) as $y) {
            $this->blockField(new Point(1, $y), false, null, BlockedFieldTypeEnum::EDGE_BLOCK);
        }
        // right edge
        foreach (range(1, $height) as $y) {
            $this->blockField(new Point($width, $y), false, null, BlockedFieldTypeEnum::EDGE_BLOCK);
        }
    }

    private function getFieldIndex(PointInterface $point): int
    {
        return $point->getX() + ($point->getY() - 1) * $this->width;
    }

    /** @return array<int, PointInterface> */
    private function getSurroundingPoints(PointInterface $point, int $range): array
    {
        $result = [];

        $x = $point->getX();
        $y = $point->getY();

        for ($i = $x - $range; $i <= $x + $range; $i++) {
            for ($j = $y - $range; $j <= $y + $range; $j++) {
                $result[] = new Point($i, $j);
            }
        }

        return $result;
    }
}
