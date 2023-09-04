<?php

namespace Stu\StarsystemGenerator\Component;

use Mockery;
use Mockery\MockInterface;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilitiesInterface;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\StuTestCase;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class PlanetPlacementTest extends StuTestCase
{
    /** @var MockInterface|PlanetMoonProbabilitiesInterface  */
    private MockInterface $planetMoonProbabilities;

    /** @var MockInterface|StuRandom  */
    private MockInterface $stuRandom;

    /** @var MockInterface|SystemMapDataInterface  */
    private MockInterface $mapData;

    /** @var MockInterface|SystemConfigurationInterface  */
    private MockInterface $config;

    private PlanetPlacementInterface $subject;

    public function setUp(): void
    {
        $this->planetMoonProbabilities = $this->mock(PlanetMoonProbabilitiesInterface::class);
        $this->stuRandom = $this->mock(StuRandom::class);
        $this->mapData = $this->mock(SystemMapDataInterface::class);
        $this->config = $this->mock(SystemConfigurationInterface::class);

        $this->subject = new PlanetPlacement($this->planetMoonProbabilities, $this->stuRandom);
    }

    public function testPlacePlanet(): void
    {
        $this->config->shouldReceive('getProbabilities')
            ->with(FieldTypeEnum::PLANET)
            ->once()
            ->andReturn([1, 2, 3]);
        $this->config->shouldReceive('getPropabilityBlacklist')
            ->with(FieldTypeEnum::PLANET)
            ->once()
            ->andReturn([4, 5, 6]);

        $this->planetMoonProbabilities->shouldReceive('pickRandomFieldId')
            ->with([], [1, 2, 3], [4, 5, 6])
            ->once()
            ->andReturn(201);

        $this->stuRandom->shouldReceive('rand')
            ->with(30, 60, true)
            ->once()
            ->andReturn(45);

        $this->mapData->shouldReceive('getPlanetDisplay')
            ->with(Mockery::any(), Mockery::any())
            ->once()
            ->andReturn([[42, 42]]);
        $this->mapData->shouldReceive('setFieldId')
            ->with(42, 42, 201, FieldTypeEnum::PLANET)
            ->once();
        $this->mapData->shouldReceive('blockField')
            ->with(42, 42, true, FieldTypeEnum::PLANET, BlockedFieldTypeEnum::SOFT_BLOCK)
            ->once();

        $planetAmount = 1;

        $this->subject->placePlanet(
            $planetAmount,
            $this->mapData,
            $this->config
        );

        $this->assertEquals(0, $planetAmount);
    }
}
