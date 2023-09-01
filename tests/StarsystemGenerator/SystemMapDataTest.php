<?php

namespace Stu\StarsystemGenerator;

use RuntimeException;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;

final class SystemMapDataTest extends StuTestCase
{
    public function testSetFieldIdExpectCorrectFieldSet(): void
    {
        $mapData = new SystemMapData(5, 5);

        $mapData->setFieldId(3, 3, 5, FieldTypeEnum::PLANET);

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
            [2, 2], [2, 3], [2, 4], [3, 2], [3, 3], [3, 4], [4, 2], [4, 3], [4, 4]
        ];
    }

    /**
     * @dataProvider provideBlockedFieldsForPlanetData
     */
    public function testSetFieldIdExpectBlockedFieldsForPlanet(int $x, int $y): void
    {
        $posX = 3;
        $posY = 3;

        if ($x === $posX && $y === $posY) {
            static::expectExceptionMessage('already in use');
        } else {
            static::expectExceptionMessage('field can not be used');
        }
        static::expectException(RuntimeException::class);

        $mapData = new SystemMapData(5, 5);

        $mapData->setFieldId($posX, $posY, 5, FieldTypeEnum::PLANET);

        $mapData->setFieldId($x, $y, 42, 42);
    }

    public static function provideBlockedFieldsForMassCenterData()
    {
        return [
            [1, 1], [2, 1], [3, 1], [4, 1], [5, 1],
            [1, 2], [2, 2], [3, 2], [4, 2], [5, 2],
            [1, 3], [2, 3], [3, 3], [4, 3], [5, 3],
            [1, 4], [2, 4], [3, 4], [4, 4], [5, 4],
            [1, 5], [2, 5], [3, 5], [4, 5], [5, 5]
        ];
    }

    /**
     * @dataProvider provideBlockedFieldsForMassCenterData
     */
    public function testSetFieldIdExpectBlockedFieldsForMassCenter(int $x, int $y): void
    {
        $posX = 3;
        $posY = 3;

        if ($x === $posX && $y === $posY) {
            static::expectExceptionMessage('already in use');
        } else {
            static::expectExceptionMessage('field can not be used');
        }
        static::expectException(RuntimeException::class);

        $mapData = new SystemMapData(5, 5);

        $mapData->setFieldId($posX, $posY, 5, FieldTypeEnum::MASS_CENTER);

        $mapData->setFieldId($x, $y, 42, 42);
    }

    public function testGetAsteroidRing(): void
    {
        $mapData = new SystemMapData(5, 5);

        $result = $mapData->getAsteroidRing(50);

        $this->assertEquals([[2, 1], [3, 1], [1, 2], [4, 2], [1, 3], [4, 3], [2, 4], [3, 4]], $result);
    }


    public function testGetPlanetDisplay(): void
    {
        $mapData = new SystemMapData(5, 5);

        $result = $mapData->getPlanetDisplay(100, 1);

        echo print_r($result, true);
    }

    public function testToString(): void
    {
        $mapData = new SystemMapData(3, 2);

        $mapData->setFieldId(2, 1, 5, FieldTypeEnum::ASTEROID);

        static::assertEquals(
            "0,5,0\n"
                . "0,0,0\n",
            $mapData->toString()
        );
    }
}
