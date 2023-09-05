<?php

namespace Stu\StarsystemGenerator\Component;

use InvalidArgumentException;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
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

        $placedFields = $this->placeMassCenter(
            $firstMassCenterFields,
            $firstMassCenterSize,
            $overallWidth,
            $overallHeight,
            true,
            $mapData
        );


        if ($secondMassCenterFields !== null) {
            $placedFields = array_merge($placedFields, $this->placeMassCenter(
                $secondMassCenterFields,
                $secondMassCenterSize,
                $overallWidth,
                $overallHeight,
                false,
                $mapData
            ));
        }

        $this->checkForBlockFields($placedFields, $mapData);
    }

    /**
     * @param array<array{0: int, 1: int, 2: int}> $polygon
     */
    private function checkForBlockFields(array $polygon, SystemMapDataInterface $mapData): void
    {
        $convexHull = $this->calculateConvexHull($polygon);

        for ($y = 1; $y <= $mapData->getHeight(); $y++) {
            for ($x = 1; $x <= $mapData->getWidth(); $x++) {
                if ($this->isPointInsidePolygon($x, $y, $convexHull)) {
                    $mapData->blockField($x, $y, true, FieldTypeEnum::MASS_CENTER, BlockedFieldTypeEnum::HARD_BLOCK);
                }
            }
        }
    }

    /**
     * @param array<array{0: int, 1: int, 2: int}> $coordinates
     * 
     * @return array<array{0: int, 1:int}>
     */
    private function calculateConvexHull(array $coordinates): array
    {
        // Sort coordinates by their x and y values
        usort($coordinates, function ($a, $b) {
            if ($a[0] == $b[0]) {
                return $a[1] - $b[1];
            }
            return $a[0] - $b[0];
        });

        // Initialize upper and lower hulls
        /** @var array<array{0: int, 1: int}> */
        $upperHull = [];

        /** @var array<array{0: int, 1: int}> */
        $lowerHull = [];

        // Compute the upper hull
        foreach ($coordinates as $coord) {
            while (
                count($upperHull) >= 2 &&
                $this->crossProduct($upperHull[count($upperHull) - 2], $upperHull[count($upperHull) - 1], $coord) <= 0
            ) {
                array_pop($upperHull);
            }
            $upperHull[] = [$coord[0], $coord[1]];
        }

        // Compute the lower hull
        foreach (array_reverse($coordinates) as $coord) {
            while (
                count($lowerHull) >= 2 &&
                $this->crossProduct($lowerHull[count($lowerHull) - 2], $lowerHull[count($lowerHull) - 1], $coord) <= 0
            ) {
                array_pop($lowerHull);
            }
            $lowerHull[] = [$coord[0], $coord[1]];
        }

        // Remove duplicate points between upper and lower hulls
        array_pop($upperHull);
        array_pop($lowerHull);

        // Combine upper and lower hulls to get the convex hull
        $convexHull = array_merge($upperHull, $lowerHull);

        return $convexHull;
    }

    /**
     * @param array{0: int, 1: int} $a
     * @param array{0: int, 1: int} $b
     * @param array{0: int, 1: int, 2: int} $c
     */
    private function crossProduct($a, $b, $c): int
    {
        return ($b[0] - $a[0]) * ($c[1] - $a[1]) - ($b[1] - $a[1]) * ($c[0] - $a[0]);
    }


    /**
     * @param array<array{0: int, 1: int}> $polygon
     */
    private function isPointInsidePolygon(int $x, int $y, array $polygon): bool
    {
        $inside = false;
        $count = count($polygon);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /** 
     * @param array<int, int> $fields 
     * 
     * @return array<array{0: int, 1: int, 2: int}>
     * */
    private function placeMassCenter(
        array $fields,
        int $massCenterSize,
        int $overallWidth,
        int $overallHeight,
        bool $isFirstMassCenter,
        SystemMapDataInterface $mapData
    ): array {
        $systemWidth = $mapData->getWidth();
        $systemHeight = $mapData->getHeight();

        $xOffset = (int)(($systemWidth - $overallWidth) / 2);
        $yOffset = (int)(($systemHeight - $overallHeight) / 2);

        $mapFields = [];

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

            $mapFields[] = [$x, $y, $id];
        }

        foreach ($mapFields as [$x, $y, $id]) {
            $mapData->setFieldId($x, $y, $id, FieldTypeEnum::MASS_CENTER);
        }

        return $mapFields;
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
