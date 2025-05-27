<?php

namespace Stu\StarsystemGenerator\Component;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilitiesInterface;
use Stu\StarsystemGenerator\Config\PlanetRadius;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Lib\FieldInterface;
use Stu\StarsystemGenerator\Lib\PlanetDisplayInterface;
use Stu\StarsystemGenerator\Lib\Point;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\StuTestCase;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class PlanetPlacementTest extends StuTestCase
{
    /** @var MockInterface&PlanetMoonProbabilitiesInterface  */
    private $planetMoonProbabilities;
    /** @var MockInterface&PlanetRingPlacementInterface  */
    private $planetRingPlacement;
    /** @var MockInterface&StuRandom  */
    private $stuRandom;

    /** @var MockInterface|SystemMapDataInterface  */
    private MockInterface $mapData;

    /** @var MockInterface|SystemConfigurationInterface  */
    private MockInterface $config;

    private PlanetPlacementInterface $subject;

    public function setUp(): void
    {
        $this->planetMoonProbabilities = $this->mock(PlanetMoonProbabilitiesInterface::class);
        $this->planetRingPlacement = $this->mock(PlanetRingPlacementInterface::class);
        $this->stuRandom = $this->mock(StuRandom::class);

        $this->mapData = $this->mock(SystemMapDataInterface::class);
        $this->config = $this->mock(SystemConfigurationInterface::class);

        $this->subject = new PlanetPlacement(
            $this->planetMoonProbabilities,
            $this->planetRingPlacement,
            $this->stuRandom
        );
    }

    public static function dataProvider(): array
    {
        return [
            [201, false],
            [263, false],
            [301, true],
            [363, true]
        ];
    }

    #[DataProvider('dataProvider')]
    public function testPlacePlanet(
        int $randomPlanetFieldId,
        bool $expectRingPlacement
    ): void {
        $planetAmount = 1;

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
            ->andReturn($randomPlanetFieldId);

        $this->stuRandom->shouldReceive('rand')
            ->with(
                PlanetRadius::PLANET_RADIUS_PERCENTAGE[$randomPlanetFieldId][0],
                PlanetRadius::PLANET_RADIUS_PERCENTAGE[$randomPlanetFieldId][1],
                true
            )
            ->once()
            ->andReturn(45);

        $point = new Point(42, 43);
        $planetDisplay = $this->mock(PlanetDisplayInterface::class);

        $planetDisplay->shouldReceive('getFirstPoint')
            ->withNoArgs()
            ->once()
            ->andReturn($point);
        $planetDisplay->shouldReceive('getLastPoint')
            ->withNoArgs()
            ->once()
            ->andReturn($point);

        $this->mapData->shouldReceive('getPlanetDisplay')
            ->with(Mockery::any(), Mockery::any(), "2")
            ->once()
            ->andReturn($planetDisplay);
        $this->mapData->shouldReceive('setField')
            ->with(Mockery::on(function (FieldInterface $field) use ($randomPlanetFieldId) {
                if ($field->getId() !== $randomPlanetFieldId) {
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
        $this->mapData->shouldReceive('addIdentifier')
            ->with(Mockery::on(function (PointInterface $point) {
                if ($point->getX() !== 42) {
                    return false;
                }
                if ($point->getY() !== 43) {
                    return false;
                }
                return true;
            }), 2)
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

        if ($expectRingPlacement) {
            $this->planetRingPlacement->shouldReceive('addPlanetRing')
                ->with($randomPlanetFieldId, Mockery::any(), Mockery::any())
                ->once();
        }

        $this->subject->placePlanet(
            $planetAmount,
            $this->mapData,
            $this->config
        );

        $this->assertEquals(2, $planetAmount);
    }
}
