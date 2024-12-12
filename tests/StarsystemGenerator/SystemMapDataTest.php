<?php

namespace Stu\StarsystemGenerator;

use RuntimeException;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Exception\HardBlockedFieldException;
use Stu\StarsystemGenerator\Exception\MassCenterPerimeterBlockedFieldException;
use Stu\StarsystemGenerator\Lib\Field;
use Stu\StarsystemGenerator\Lib\Point;

final class SystemMapDataTest extends StuTestCase
{
    public function testSetFieldIdExpectCorrectFieldSet(): void
    {
        $mapData = new SystemMapData(5, 5);

        $mapData->setField(new Field(new Point(3, 3), 5));

        static::assertEquals(
            "0,0,0,0,0\n"
                . "0,0,0,0,0\n"
                . "0,0,5,0,0\n"
                . "0,0,0,0,0\n"
                . "0,0,0,0,0\n",
            $mapData->toString()
        );
    }

    public static function provideBlockedFieldsForPlanetData()
    {
        return [
            [2, 2],
            [2, 3],
            [2, 4],
            [3, 2],
            [3, 3],
            [3, 4],
            [4, 2],
            [4, 3],
            [4, 4]
        ];
    }

    /**
     * @dataProvider provideBlockedFieldsForPlanetData
     */
    public function testSetFieldIdExpectBlockedFieldsForPlanet(int $x, int $y): void
    {
        $posX = 3;
        $posY = 3;

        static::expectExceptionMessage('field can not be used');
        static::expectException(RuntimeException::class);

        $mapData = new SystemMapData(5, 5);

        $mapData->blockField(new Point($posX, $posY), true, FieldTypeEnum::PLANET, BlockedFieldTypeEnum::SOFT_BLOCK);

        $mapData->setField(new Field(new Point($x, $y), 42));
    }

    public static function provideBlockedFieldsForMassCenterData()
    {
        return [
            [1, 1],
            [2, 1],
            [3, 1],
            [4, 1],
            [5, 1],
            [1, 2],
            [2, 2],
            [3, 2],
            [4, 2],
            [5, 2],
            [1, 3],
            [2, 3],
            [3, 3],
            [4, 3],
            [5, 3],
            [1, 4],
            [2, 4],
            [3, 4],
            [4, 4],
            [5, 4],
            [1, 5],
            [2, 5],
            [3, 5],
            [4, 5],
            [5, 5]
        ];
    }

    /**
     * @dataProvider provideBlockedFieldsForMassCenterData
     */
    public function testSetFieldIdExpectBlockedFieldsForMassCenter(int $x, int $y): void
    {
        $posX = 3;
        $posY = 3;

        if ($posX === $x && $posY === $y) {
            static::expectExceptionMessage('field can not be used, hard block');
            static::expectException(HardBlockedFieldException::class);
        } else {
            static::expectExceptionMessage('field can not be used, mass center perimeter block');
            static::expectException(MassCenterPerimeterBlockedFieldException::class);
        }

        $mapData = new SystemMapData(5, 5);

        $mapData->blockField(new Point($posX, $posY), true, FieldTypeEnum::MASS_CENTER, BlockedFieldTypeEnum::HARD_BLOCK);

        $mapData->setField(new Field(new Point($x, $y), 42));
    }

    public function testGetAsteroidRing(): void
    {
        $mapData = new SystemMapData(5, 5);

        $result = $mapData->getAsteroidRing(50, 0);

        $this->assertEquals([
            0 => new Point(3, 2),
            45 => new Point(4, 2),
            90 => new Point(4, 3),
            135 => new Point(4, 4),
            180 => new Point(3, 4),
            225 => new Point(2, 4),
            270 => new Point(2, 3),
            315 => new Point(2, 2)
        ], $result);
    }


    public function testGetPlanetDisplay(): void
    {
        $mapData = new SystemMapData(10, 10);

        $result = $mapData->getPlanetDisplay(50, 1);

        //echo print_r($result, true);
        $this->assertEquals(9, count($result->getPoints()));
    }

    public function testToString(): void
    {
        $mapData = new SystemMapData(3, 4);
        $mapData->setField(new Field(new Point(2, 2), 5));

        static::assertEquals(
            "0,0,0\n"
                . "0,5,0\n"
                . "0,0,0\n"
                . "0,0,0\n",
            $mapData->toString()
        );
        static::assertEquals(
            "2,2,2\n"
                . "2,0,2\n"
                . "2,0,2\n"
                . "2,2,2\n",
            $mapData->toString(false, true)
        );
    }
}
