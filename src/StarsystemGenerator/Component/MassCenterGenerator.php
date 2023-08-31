<?php

namespace Stu\StarsystemGenerator\Component;

use InvalidArgumentException;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class MassCenterGenerator implements MassCenterGeneratorInterface
{
    public function generate(
        array $firstMassCenterFields,
        ?array $secondMassCenterFields,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void {

        //check size
        $firstMassCenterSize = $this->getSize($firstMassCenterFields);
        $secondMassCenterSize = $this->getSize($secondMassCenterFields);

        //calculate overall size
        $overallWidth = $this->getOverallWidth(
            $firstMassCenterSize,
            $secondMassCenterSize,
            $config
        );
        $overallHeight = $this->getOverallHeight(
            $firstMassCenterSize,
            $secondMassCenterSize,
            $config
        );

        $this->placeMassCenter(
            $firstMassCenterFields,
            $firstMassCenterSize,
            $overallWidth,
            $overallHeight,
            true,
            $mapData
        );

        if ($secondMassCenterFields !== null) {
            $this->placeMassCenter(
                $secondMassCenterFields,
                $secondMassCenterSize,
                $overallWidth,
                $overallHeight,
                false,
                $mapData
            );
        }
    }

    /** @param array<int, int> $fields */
    private function placeMassCenter(
        array $fields,
        int $massCenterSize,
        int $overallWidth,
        int $overallHeight,
        bool $isFirstMassCenter,
        SystemMapDataInterface $mapData
    ): void {
        $systemWidth = $mapData->getWidth();
        $systemHeight = $mapData->getHeight();

        $xOffset = (int)($systemWidth - $overallWidth) / 2;
        $yOffset = (int)($systemHeight - $overallHeight) / 2;

        foreach ($fields as $key => $id) {
            $column = $key % $massCenterSize + 1;
            $row = ((int) $key / $massCenterSize) + 1;

            if ($isFirstMassCenter) {
                $x = $xOffset + $column;
                $y = $yOffset + $row;
            } else {
                $x = $xOffset + $overallWidth - ($massCenterSize - $column);
                $y = $yOffset + $overallHeight - ($massCenterSize - $row);
            }

            $mapData->setFieldId($x, $y, $id, FieldTypeEnum::MASS_CENTER);
        }
    }

    /** @param array<int, int>|null $fields */
    private function getSize(?array $fields): int
    {
        if ($fields === null) {
            return 0;
        }

        $fieldCount = count($fields);

        $squareRoot = (int)sqrt($fieldCount);
        if ($squareRoot ** 2 != $fieldCount) {
            throw new InvalidArgumentException(sprintf('fieldCount %d is illegal value', $fieldCount));
        }

        return $squareRoot;
    }

    private function getOverallWidth(
        int $firstMassCenterSize,
        int $secondMassCenterSize,
        SystemConfigurationInterface $config
    ): int {
        if ($secondMassCenterSize === 0) {
            return $firstMassCenterSize;
        }

        return $firstMassCenterSize + $secondMassCenterSize +  $config->getMassCenterDistanceHorizontal();
    }

    private function getOverallHeight(
        int $firstMassCenterSize,
        int $secondMassCenterSize,
        SystemConfigurationInterface $config
    ): int {
        if ($secondMassCenterSize === 0) {
            return $firstMassCenterSize;
        }

        return $firstMassCenterSize + $secondMassCenterSize +  $config->getMassCenterDistanceVertical();
    }
}
