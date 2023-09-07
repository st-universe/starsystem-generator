<?php

namespace Stu\StarsystemGenerator\Component;

use Mockery;
use Mockery\MockInterface;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilitiesInterface;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Lib\FieldInterface;
use Stu\StarsystemGenerator\Lib\Point;
use Stu\StarsystemGenerator\Lib\PointInterface;
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

        $point = new Point(42, 43);

        $this->mapData->shouldReceive('getPlanetDisplay')
            ->with(Mockery::any(), Mockery::any())
            ->once()
            ->andReturn([$point]);
        $this->mapData->shouldReceive('setField')
            ->with(Mockery::on(function (FieldInterface $field) {
                if ($field->getId() !== 201) {
                    return false;
                }
                if ($field->getPoint()->getX() !== 42) {
                    return false;
                }
                if ($field->getPoint()->getY() !== 43) {
                    return false;
                }
                return true;
            }))
            ->once();
        $this->mapData->shouldReceive('blockField')
            ->with(Mockery::on(function (PointInterface $point) {
                if ($point->getX() !== 42) {
                    return false;
                }
                if ($point->getY() !== 43) {
                    return false;
                }
                return true;
            }), true, FieldTypeEnum::PLANET, BlockedFieldTypeEnum::HARD_BLOCK)
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
