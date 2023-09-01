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

final class PlanetMoonGeneratorTest extends StuTestCase
{
    /** @var MockInterface|PlanetMoonProbabilitiesInterface  */
    private MockInterface $planetMoonProbabilities;

    /** @var MockInterface|StuRandom  */
    private MockInterface $stuRandom;

    /** @var MockInterface|SystemMapDataInterface  */
    private MockInterface $mapData;

    /** @var MockInterface|SystemConfigurationInterface  */
    private MockInterface $config;

    private PlanetMoonGeneratorInterface $subject;

    public function setUp(): void
    {
        $this->planetMoonProbabilities = $this->mock(PlanetMoonProbabilitiesInterface::class);
        $this->stuRandom = $this->mock(StuRandom::class);
        $this->mapData = $this->mock(SystemMapDataInterface::class);
        $this->config = $this->mock(SystemConfigurationInterface::class);

        $this->subject = new PlanetMoonGenerator($this->planetMoonProbabilities, $this->stuRandom);
    }

    public static function provideFieldArraysNotSquareData()
    {
        return [
            [[1, 1], null, 2],
            [[1, 1, 1], null, 3],
            [[1, 1, 1, 1, 1], null, 5],
            [[1], [1, 1, 1, 1, 1, 1], 6],
            [[1], [1, 1, 1, 1, 1, 1, 1], 7],
            [[1], [1, 1, 1, 1, 1, 1, 1, 1], 8]
        ];
    }

    public function testGenerate(): void
    {
        $this->config->shouldReceive('hasPlanets')
            ->withNoArgs()
            ->once()
            ->andReturn(true);
        $this->config->shouldReceive('getMaxPlanets')
            ->withNoArgs()
            ->once()
            ->andReturn(111);
        $this->config->shouldReceive('hasMoons')
            ->withNoArgs()
            ->once()
            ->andReturn(false);
        $this->config->shouldReceive('getProbabilities')
            ->with(FieldTypeEnum::PLANET)
            ->once()
            ->andReturn([]);

        $this->planetMoonProbabilities->shouldReceive('pickRandomFieldId')
            ->with([], null)
            ->once()
            ->andReturn(201);

        $this->stuRandom->shouldReceive('rand')
            ->with(30, 60, true)
            ->once()
            ->andReturn(45);

        $this->mapData->shouldReceive('getRandomPlanetAmount')
            ->with($this->stuRandom)
            ->once()
            ->andReturn(1);
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

        $this->subject->generate(
            $this->mapData,
            $this->config
        );
    }
}
