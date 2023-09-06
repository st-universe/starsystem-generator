<?php

namespace Stu\StarsystemGenerator\Lib;

class GeometryCalculations
{
    /**
     * @param array{0: PointInterface, 1: PointInterface} $vector1
     * @param array{0: PointInterface, 1: PointInterface} $vector2
     */
    public static function calculateAngleBetweenVectors(array $vector1, array $vector2): int
    {
        $x1 = $vector1[0]->getX();
        $y1 = $vector1[0]->getY();

        $x2 = $vector1[1]->getX();
        $y2 = $vector1[1]->getY();

        $x3 = $vector2[0]->getX();
        $y3 = $vector2[0]->getY();

        $x4 = $vector2[1]->getX();
        $y4 = $vector2[1]->getY();

        // Calculate the dot product of the two vectors
        $dotProduct = ($x2 - $x1) * ($x4 - $x3) + ($y2 - $y1) * ($y4 - $y3);

        // Calculate the determinant of the two vectors
        $determinant = ($x2 - $x1) * ($y4 - $y3) - ($y2 - $y1) * ($x4 - $x3);

        // Calculate the angle in radians using the atan2 function
        $angleInRadians = atan2($determinant, $dotProduct);

        // Convert the angle from radians to degrees
        $angleInDegrees = rad2deg($angleInRadians);

        // Ensure the angle is positive and between 0 and 360 degrees
        if ($angleInDegrees < 0) {
            $angleInDegrees += 360;
        }

        return (int)round($angleInDegrees);
    }
}
