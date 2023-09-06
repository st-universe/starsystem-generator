<?php

namespace Stu\StarsystemGenerator\Component;

use Mockery;
use Mockery\MockInterface;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\StuTestCase;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class PlanetMoonGeneratorTest extends StuTestCase
{
    /** @var MockInterface|PlanetPlacementInterface  */
    private MockInterface $planetPlacement;

    /** @var MockInterface|MoonPlacementInterface  */
    private MockInterface $moonPlacement;

    /** @var MockInterface|StuRandom  */
    private MockInterface $stuRandom;

    /** @var MockInterface|SystemMapDataInterface  */
    private MockInterface $mapData;

    /** @var MockInterface|SystemConfigurationInterface  */
    private MockInterface $config;

    private PlanetMoonGeneratorInterface $subject;

    public function setUp(): void
    {
        $this->planetPlacement = $this->mock(PlanetPlacementInterface::class);
        $this->moonPlacement = $this->mock(MoonPlacementInterface::class);
        $this->stuRandom = $this->mock(StuRandom::class);
        $this->mapData = $this->mock(SystemMapDataInterface::class);
        $this->config = $this->mock(SystemConfigurationInterface::class);

        $this->subject = new PlanetMoonGenerator(
            $this->planetPlacement,
            $this->moonPlacement,
            $this->stuRandom
        );
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

        $this->mapData->shouldReceive('getRandomPlanetAmount')
            ->with($this->stuRandom)
            ->once()
            ->andReturn(2);

        $this->planetPlacement->shouldReceive('placePlanet')
            ->with(
                Mockery::on(
                    function (&$planetAmount) {
                        $planetAmount--;
                        return true;
                    }
                ),
                $this->mapData,
                $this->config
            )
            ->twice()
            ->andReturn([1 => [2, 3]]);

        $this->subject->generate(
            $this->mapData,
            $this->config
        );
    }
}
