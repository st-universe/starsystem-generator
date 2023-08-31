<?php

namespace Stu\StarsystemGenerator;

use Stu\StarsystemGenerator\Enum\FieldTypeEnum;

final class SystemMapDataTest extends StuTestCase
{
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
