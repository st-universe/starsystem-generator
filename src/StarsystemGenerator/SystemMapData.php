<?php

namespace Stu\StarsystemGenerator;

use RuntimeException;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Exception\FieldAlreadyUsedException;
use Stu\StarsystemGenerator\Exception\HardBlockedFieldException;
use Stu\StarsystemGenerator\Exception\UnknownFieldIndexException;
use Stu\StarsystemGenerator\Lib\FieldInterface;
use Stu\StarsystemGenerator\Lib\GeometryCalculations;
use Stu\StarsystemGenerator\Lib\Point;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;

//TODO unit tests
final class SystemMapData implements SystemMapDataInterface
{
    private int $width;
    private int $height;

    /** @var array<int, int> */
    private array $fieldData;

    /** @var array<int, int> */
    private array $blockedFields = [];

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->fieldData = $this->initFieldArray(0);
        $this->blockedFields = $this->initFieldArray(BlockedFieldTypeEnum::NOT_BLOCKED);
    }

    /**
     * @return array<int, int>
     */
    private function initFieldArray(int $value): array
    {
        $result = [];

        for ($y = 1; $y <= $this->height; $y++) {
            for ($x = 1; $x <= $this->width; $x++) {
                $index = $x + ($y - 1) * $this->width;
                $result[$index] = $value;
            }
        }

        return $result;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getRandomPlanetAmount(StuRandom $stuRandom): int
    {
        return (int)($this->getFieldAmount() / $stuRandom->rand(36, 81, true));
    }

    public function getRandomMoonAmount(StuRandom $stuRandom): int
    {
        return (int)($this->getFieldAmount() / $stuRandom->rand(19, 62, true));
    }

    private function getFieldAmount(): int
    {
        return $this->getWidth() * $this->getHeight();
    }

    private function getFieldIndex(PointInterface $point): int
    {
        return $point->getX() + ($point->getY() - 1) * $this->width;
    }

    public function setField(FieldInterface $field, bool $allowSoftBlock = false): SystemMapDataInterface
    {
        $index = $this->getFieldIndex($field->getPoint());

        if (!array_key_exists($index, $this->fieldData)) {
            throw new UnknownFieldIndexException(sprintf('index %d is unknown', $index));
        }

        if ($this->fieldData[$index] !== 0) {
            throw new FieldAlreadyUsedException('already in use');
        }

        $blockType = $this->blockedFields[$index];

        if ($blockType === BlockedFieldTypeEnum::HARD_BLOCK) {
            throw new HardBlockedFieldException('field can not be used, hard block');
        }

        if (!$allowSoftBlock && $blockType === BlockedFieldTypeEnum::SOFT_BLOCK) {
            throw new RuntimeException('field can not be used, soft block');
        }

        $this->fieldData[$index] = $field->getId();

        return $this;
    }

    public function getAsteroidRing(int $radiusPercentage, int $variance): array
    {
        return $this->getRing($radiusPercentage, $variance);
    }


    public function getPlanetDisplay(int $radiusPercentage, int $moonRange): ?array
    {
        $ring = $this->getRing($radiusPercentage);

        shuffle($ring);

        foreach ($ring as $point) {
            $displayPoints = $this->getSurroundingPoints($point, $moonRange);

            $index = $this->getFieldIndex($point);

            if ($this->blockedFields[$index] !== BlockedFieldTypeEnum::NOT_BLOCKED) {
                //echo "IB";
            } else if ($this->areAllPointsUnused($displayPoints)) {
                //echo "SUCCESS";
                return $displayPoints;
            } else {
                //echo "USED";
            }
        }

        return null;
    }

    /** @param array<int, PointInterface> $points */
    private function areAllPointsUnused(array $points): bool
    {
        //echo print_r($fields, true);

        foreach ($points as $point) {
            $index = $this->getFieldIndex($point);

            if (!array_key_exists($index, $this->fieldData)) {
                continue;
            }

            if ($this->fieldData[$index] !== 0) {
                //echo sprintf("false: %d,%d", $x, $y);
                return false;
            }
        }

        return true;
    }

    /** @return array<PointInterface> */
    private function getRing(int $radiusPercentage, int $variance = 0): array
    {
        $result = [];

        $radius = (int)($this->getWidth() / 2 * $radiusPercentage / 100);

        if ($radius === 0) {
            return $result;
        }

        $centerX = $this->getWidth() / 2;
        $centerY = $this->getHeight() / 2;

        $centerPoint = new Point($centerX, $centerY);
        $topCenterPoint = new Point($centerX, 0);

        $verticalVector = [$centerPoint, $topCenterPoint];

        foreach (range(1, $this->getHeight()) as $y) {
            foreach (range(1, $this->getWidth()) as $x) {
                $distance = sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2));

                if ($variance === 0) {
                    $distance = (int)$distance;
                }

                $diffAbsolut = abs($distance - $radius);
                $diffVariance = $diffAbsolut / $radius * 100;

                if ($diffVariance <= $variance) {
                    $angle = GeometryCalculations::calculateAngleBetweenVectors($verticalVector, [$centerPoint, new Point($x, $y)]);
                    $result[$angle] = new Point($x, $y);
                }
            }
        }

        return $result;
    }

    public function getFieldData(): array
    {
        return $this->fieldData;
    }

    public function toString(bool $doPrint = false, bool $showBlocked = false): string
    {
        $values = $showBlocked ? $this->blockedFields : $this->fieldData;

        if ($doPrint) {

            echo "<table>";
            foreach (range(1, $this->getHeight()) as $y) {
                echo "<tr>";

                echo implode(
                    "&nbsp;&nbsp;",
                    array_map(
                        fn (int $value) => sprintf('<td style="width: 30px; height: 30px; text-align: center;">%d</td>', $value),
                        array_slice($values, ($y - 1) * $this->getWidth(), $this->getWidth())
                    )
                );

                echo "</tr>";
            }

            echo "</table>";
            return '';
        }

        $result = '';
        foreach (range(1, $this->getHeight()) as $y) {
            $result .= implode(
                ",",
                array_slice($values, ($y - 1) * $this->getWidth(), $this->getWidth())
            ) . "\n";
        }

        return $result;
    }

    public function blockField(PointInterface $point, bool $blockSurrounding, ?int $fieldType, int $blockType): void
    {
        $x = $point->getX();
        $y = $point->getY();

        if ($x < 1 || $x > $this->width || $y < 1 || $y > $this->height) {
            return;
        }

        $index = $this->getFieldIndex($point);

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
                    $fieldType === FieldTypeEnum::MASS_CENTER ? BlockedFieldTypeEnum::HARD_BLOCK : BlockedFieldTypeEnum::SOFT_BLOCK
                );
            }
        }
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
