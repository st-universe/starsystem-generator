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

    /**
     * @param array<PointInterface> $polygon
     */
    public static function isPointCoveredByPolygon(int $x, int $y, array $polygon): bool
    {
        $numVertices = count($polygon);

        if ($numVertices < 3) {
            return false; // A polygon with less than 3 vertices cannot cover any point.
        }

        // Check if the point is to the left of all edges of the convex hull.
        for ($i = 0; $i < $numVertices; $i++) {
            $currentVertex = $polygon[$i];
            $nextVertex = $polygon[($i + 1) % $numVertices];

            $edgeVector = [
                $nextVertex->getX() - $currentVertex->getX(),
                $nextVertex->getY() - $currentVertex->getY()
            ];

            $pointVector = [
                $x - $currentVertex->getX(),
                $y - $currentVertex->getY()
            ];

            // Calculate the cross product of the edge and point vectors.
            $crossProduct = $edgeVector[0] * $pointVector[1] - $edgeVector[1] * $pointVector[0];

            // If the cross product is positive, the point is to the left of the edge.
            if ($crossProduct < 0) {
                return false;
            }
        }

        // If the point is to the left of all edges, it's inside or on the boundary of the convex hull.
        return true;
    }
}
