<?php

namespace Stu\StarsystemGenerator;

use Stu\StarsystemGenerator\Lib\GeometryCalculations;
use Stu\StarsystemGenerator\Lib\Point;

final class GeometryCalculationsTest extends StuTestCase
{

    public static function provideCalculateAngleBetweenVectorsData()
    {
        return [
            [15, 0, 0],
            [15, 5, 0],
            [30, 15, 90],
            [45, 15, 90],
            [15, 30, 180],
            [14, 30, 184],
            [5, 15, 270],
            [14, 0, 356],
        ];
    }

    /**
     * @dataProvider provideCalculateAngleBetweenVectorsData
     */
    public function testCalculateAngleBetweenVectors(int $x, int $y, int $expectedAngle): void
    {
        $centerPoint = new Point(15, 15);
        $topCenterPoint = new Point(15, 0);

        $verticalVector = [$centerPoint, $topCenterPoint];
        $targetVector = [$centerPoint, new Point($x, $y)];

        $result = GeometryCalculations::calculateAngleBetweenVectors($verticalVector, $targetVector);

        $this->assertEquals($expectedAngle, $result);
    }

    public static function provideIsPointCoveredByPolygonData()
    {
        return [
            [13, 13, true],
            [14, 13, true],
            [14, 14, true],
            [13, 14, true],
            [13, 12, false],
            [14, 12, false],
            [15, 12, false],
            [15, 13, false],
            [15, 14, false],
            [15, 15, false],
            [14, 15, false],
            [13, 15, false],
            [12, 15, false],
            [12, 14, false],
            [12, 13, false],
            [12, 12, false],
            [13, 12, false],
        ];
    }

    /**
     * @dataProvider provideIsPointCoveredByPolygonData
     */
    public function testIsPointCoveredByPolygon(int $x, int $y, bool $expectation): void
    {
        $polygon = [new Point(13, 13), new Point(14, 13), new Point(14, 14), new Point(13, 14)];

        $result = GeometryCalculations::isPointCoveredByPolygon($x, $y, $polygon);

        $this->assertEquals($expectation, $result);
    }
}
