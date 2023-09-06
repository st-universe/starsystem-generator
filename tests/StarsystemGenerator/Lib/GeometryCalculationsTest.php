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
}
