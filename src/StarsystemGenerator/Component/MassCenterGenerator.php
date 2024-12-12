<?php

namespace Stu\StarsystemGenerator\Component;

use InvalidArgumentException;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Lib\Field;
use Stu\StarsystemGenerator\Lib\FieldInterface;
use Stu\StarsystemGenerator\Lib\GeometryCalculations;
use Stu\StarsystemGenerator\Lib\Point;
use Stu\StarsystemGenerator\Lib\PointInterface;
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

        $this->blockMassCenterArea($placedFields, $mapData);
    }

    /**
     * @param array<FieldInterface> $fields
     */
    private function blockMassCenterArea(array $fields, SystemMapDataInterface $mapData): void
    {
        $points = array_map(fn(FieldInterface $field) => $field->getPoint(), $fields);
        $convexHull = $this->calculateConvexHull($points);

        //echo print_r($convexHull, true);

        for ($y = 1; $y <= $mapData->getHeight(); $y++) {
            for ($x = 1; $x <= $mapData->getWidth(); $x++) {
                if (GeometryCalculations::isPointCoveredByPolygon($x, $y, $convexHull)) {

                    //echo sprintf('B(%d,%d)', $x, $y);

                    $mapData->blockField(
                        new Point($x, $y),
                        true,
                        FieldTypeEnum::MASS_CENTER,
                        BlockedFieldTypeEnum::HARD_BLOCK
                    );
                }
            }
        }
    }

    /**
     * @param array<PointInterface> $points
     * 
     * @return array<PointInterface>
     */
    private function calculateConvexHull(array $points): array
    {
        // Sort coordinates by their x and y values
        usort($points, function (PointInterface $a, PointInterface $b) {
            if ($a->getX() == $b->getX()) {
                return $a->getY() - $b->getY();
            }
            return $a->getX() - $b->getX();
        });

        // Initialize upper and lower hulls
        /** @var array<PointInterface> */
        $upperHull = [];

        /** @var array<PointInterface> */
        $lowerHull = [];

        // Compute the upper hull
        foreach ($points as $coord) {
            while (
                count($upperHull) >= 2 &&
                $this->crossProduct($upperHull[count($upperHull) - 2], $upperHull[count($upperHull) - 1], $coord) <= 0
            ) {
                array_pop($upperHull);
            }
            $upperHull[] = $coord;
        }

        // Compute the lower hull
        foreach (array_reverse($points) as $coord) {
            while (
                count($lowerHull) >= 2 &&
                $this->crossProduct($lowerHull[count($lowerHull) - 2], $lowerHull[count($lowerHull) - 1], $coord) <= 0
            ) {
                array_pop($lowerHull);
            }
            $lowerHull[] = $coord;
        }

        // Remove duplicate points between upper and lower hulls
        array_pop($upperHull);
        array_pop($lowerHull);

        // Combine upper and lower hulls to get the convex hull
        $convexHull = array_merge($upperHull, $lowerHull);

        return $convexHull;
    }

    private function crossProduct(PointInterface $a, PointInterface $b, PointInterface $c): int
    {
        return ($b->getX() - $a->getX()) * ($c->getY() - $a->getY())
            - ($b->getY() - $a->getY()) * ($c->getX() - $a->getX());
    }

    /** 
     * @param array<int, int> $fields 
     * 
     * @return array<FieldInterface>
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

        $xOffset = (int)floor(($systemWidth - $overallWidth) / 2);
        $yOffset = (int)floor(($systemHeight - $overallHeight) / 2);

        $mapFields = [];

        foreach ($fields as $key => $type) {
            $column = $key % $massCenterSize + 1;
            $row = ((int)floor($key / $massCenterSize)) + 1;

            if ($isFirstMassCenter) {
                $x = $xOffset + $column;
                $y = $yOffset + $row;
            } else {
                $x = $xOffset + $overallWidth - ($massCenterSize - $column);
                $y = $yOffset + $overallHeight - ($massCenterSize - $row);
            }

            $mapFields[] = new Field(new Point($x, $y), $type);
        }

        foreach ($mapFields as $field) {
            $mapData->setField($field);
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
