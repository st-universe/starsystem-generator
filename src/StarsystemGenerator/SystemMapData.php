<?php

namespace Stu\StarsystemGenerator;

use RuntimeException;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
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

    public function setFieldId(int $x, int $y, int $fieldId, int $fieldType, bool $allowSoftBlock = false): SystemMapDataInterface
    {
        $index = $x + ($y - 1) * $this->width;

        if ($this->fieldData[$index] !== 0) {
            throw new RuntimeException('already in use');
        }

        $blockType = $this->blockedFields[$index];

        if ($blockType === BlockedFieldTypeEnum::HARD_BLOCK) {
            throw new RuntimeException('field can not be used, hard block');
        }

        if (!$allowSoftBlock && $blockType === BlockedFieldTypeEnum::SOFT_BLOCK) {
            throw new RuntimeException('field can not be used, soft block');
        }

        $this->fieldData[$index] = $fieldId;

        //echo sprintf('set: [%d, %d, %d]', $x, $y, $fieldId);

        return $this;
    }

    public function getAsteroidRing(int $radiusPercentage): array
    {
        return $this->getRing($radiusPercentage);
    }


    public function getPlanetDisplay(int $radiusPercentage, int $moonRange): ?array
    {
        $ring = $this->getRing($radiusPercentage);

        //echo print_r($ring, true);

        shuffle($ring);

        //echo print_r($this->fieldData, true);

        foreach ($ring as [$x, $y]) {
            //echo sprintf('RP(%d,%d),', $x, $y);
            $displayFields = $this->getSurroundingFields($x, $y, $moonRange);

            //echo print_r($displayFields, true);

            $index = $x + ($y - 1) * $this->width;

            if ($this->blockedFields[$index] !== BlockedFieldTypeEnum::NOT_BLOCKED) {
                //echo "IB";
            } else if ($this->areAllFieldsUnused($displayFields)) {
                //echo "SUCCESS";
                return $displayFields;
            } else {
                //echo "USED";
            }
        }

        return null;
    }

    /** @param array<int, array{0: int, 1:int}> $fields */
    private function areAllFieldsUnused(array $fields): bool
    {
        //echo print_r($fields, true);

        foreach ($fields as [$x, $y]) {
            $index = $x + ($y - 1) * $this->width;

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

    /** @return array<int, array{0: int, 1:int}> */
    private function getRing(int $radiusPercentage): array
    {
        $result = [];

        $radius = (int)($this->getWidth() / 2 * $radiusPercentage / 100);

        $centerX = $this->getWidth() / 2;
        $centerY = $this->getHeight() / 2;

        foreach (range(1, $this->getHeight()) as $y) {
            foreach (range(1, $this->getWidth()) as $x) {
                $distance = (int)(sqrt(pow($x - $centerX, 2) + pow($y - $centerY, 2)));

                if ($distance === $radius) {
                    $result[] = [$x, $y];
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

    public function blockField(int $x, int $y, bool $blockSurrounding, ?int $fieldType, int $blockType): void
    {
        if ($x < 1 || $x > $this->width || $y < 1 || $y > $this->height) {
            return;
        }

        $index = $x + ($y - 1) * $this->width;

        if ($this->blockedFields[$index] < $blockType) {
            $this->blockedFields[$index] = $blockType;
        }

        if ($blockSurrounding) {
            $range = $fieldType === FieldTypeEnum::MASS_CENTER ? 2 : 1;

            foreach ($this->getSurroundingFields($x, $y, $range) as [$x, $y]) {
                $this->blockField(
                    $x,
                    $y,
                    false,
                    null,
                    $fieldType === FieldTypeEnum::MASS_CENTER ? BlockedFieldTypeEnum::HARD_BLOCK : BlockedFieldTypeEnum::SOFT_BLOCK
                );
            }
        }
    }

    /** @return array<int, array{0: int, 1:int}> */
    private function getSurroundingFields(int $x, int $y, int $range): array
    {
        $result = [];

        for ($i = $x - $range; $i <= $x + $range; $i++) {
            for ($j = $y - $range; $j <= $y + $range; $j++) {
                $index = $i + ($j - 1) * $this->width;
                $result[$index] = [$i, $j];
            }
        }

        return $result;
    }
}
