<?php

namespace Stu\StarsystemGenerator;

use Mockery\Adapter\Phpunit\MockeryTestCase;

final class SystemMapDataTest extends MockeryTestCase
{
    public function testToString(): void
    {
        $mapData = new SystemMapData(3, 2);

        $mapData->setFieldId(2, 1, 5);

        static::assertEquals(
            "0,5,0\n"
                . "0,0,0",
            $mapData->toString()
        );
    }
}
