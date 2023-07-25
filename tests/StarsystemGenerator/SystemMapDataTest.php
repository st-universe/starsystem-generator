<?php

namespace Stu\StarsystemGenerator;

use Mockery\Adapter\Phpunit\MockeryTestCase;

final class SystemMapDataTest extends MockeryTestCase
{
    public function testToString(): void
    {
        $mapData = new SystemMapData(3, 2);

        $mapData->setFieldId(2, 1, 5);

        $mapData->toString();
    }
}
