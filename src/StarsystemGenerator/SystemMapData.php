<?php

namespace Stu\StarsystemGenerator;

use RuntimeException;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;

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
        $this->fieldData = array_fill(1, $height * $width, 0);
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getFieldAmount(): int
    {
        return $this->getWidth() * $this->getHeight();
    }

    public function setFieldId(int $x, int $y, int $fieldId, int $fieldType): SystemMapDataInterface
    {
        $index = $x + ($y - 1) * $this->width;

        if ($this->fieldData[$index] !== 0) {
            throw new RuntimeException('already in use');
        }

        if (array_key_exists($index, $this->blockedFields)) {
            throw new RuntimeException('field can not be used');
        }

        $this->fieldData[$index] = $fieldId;
        $this->blockFields($x, $y, $fieldType <= FieldTypeEnum::PLANET, $fieldType);

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

        foreach ($ring as [$x, $y]) {
            $displayFields = $this->getSurroundingFields($x, $y, $moonRange);

            if ($this->areAllFieldsFree($displayFields)) {
                return $displayFields;
            }
        }

        return null;
    }

    /** @param array<int, array{0: int, 1:int}> $fields */
    private function areAllFieldsFree(array $fields): bool
    {
        foreach ($fields as [$x, $y]) {
            $index = $x + ($y - 1) * $this->width;

            if (array_key_exists($index, $this->fieldData)) {
                return false;
            }

            if (array_key_exists($index, $this->blockedFields)) {
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

    private function blockFields(int $x, int $y, bool $blockSurrounding, ?int $fieldType): void
    {
        if ($x < 1 || $x > $this->width || $y < 1 || $y > $this->height) {
            return;
        }

        $index = $x + ($y - 1) * $this->width;
        $this->blockedFields[$index] = 1;

        if ($blockSurrounding) {
            $range = $fieldType === FieldTypeEnum::MASS_CENTER ? 2 : 1;

            foreach ($this->getSurroundingFields($x, $y, $range) as [$x, $y]) {
                $this->blockFields($x, $y, false, null);
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
