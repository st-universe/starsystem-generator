<?php

namespace Stu\StarsystemGenerator;

use Mockery\MockInterface;
use Stu\StarsystemGenerator\Component\AsteroidRingGenerator;
use Stu\StarsystemGenerator\Component\AsteroidRingGeneratorInterface;
use Stu\StarsystemGenerator\Component\LoadSystemConfiguration;
use Stu\StarsystemGenerator\Component\LoadSystemConfigurationInterface;
use Stu\StarsystemGenerator\Component\MassCenterGenerator;
use Stu\StarsystemGenerator\Component\MassCenterGeneratorInterface;
use Stu\StarsystemGenerator\Component\PlanetMoonGenerator;
use Stu\StarsystemGenerator\Component\PlanetMoonGeneratorInterface;
use Stu\StarsystemGenerator\Component\SizeGenerator;
use Stu\StarsystemGenerator\Component\SizeGeneratorInterface;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilities;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;

final class StarsystemGeneratorTest extends StuTestCase
{
    /** @var MockInterface|LoadSystemConfigurationInterface  */
    private MockInterface $loadSystemTypeConfiguration;

    /** @var MockInterface|SizeGeneratorInterface  */
    private MockInterface $sizeGenerator;

    /** @var MockInterface|MassCenterGeneratorInterface  */
    private MockInterface $massCenterGenerator;

    /** @var MockInterface|AsteroidRingGeneratorInterface  */
    private MockInterface $asteroidRingGenerator;

    /** @var MockInterface|PlanetMoonGeneratorInterface  */
    private MockInterface $planetMoonGenerator;

    private StarsystemGeneratorInterface $subject;

    public function setUp(): void
    {
        $this->loadSystemTypeConfiguration = static::mock(LoadSystemConfigurationInterface::class);
        $this->sizeGenerator = static::mock(SizeGeneratorInterface::class);
        $this->massCenterGenerator = static::mock(MassCenterGeneratorInterface::class);
        $this->asteroidRingGenerator = static::mock(AsteroidRingGeneratorInterface::class);
        $this->planetMoonGenerator = static::mock(PlanetMoonGeneratorInterface::class);

        $this->subject = new StarsystemGenerator(
            $this->loadSystemTypeConfiguration,
            $this->sizeGenerator,
            $this->massCenterGenerator,
            $this->asteroidRingGenerator,
            $this->planetMoonGenerator
        );
    }

    public function testGenerateExpectCallOfComponents(): void
    {
        $config = $this->mock(SystemConfigurationInterface::class);
        $mapData = $this->mock(SystemMapDataInterface::class);

        $this->loadSystemTypeConfiguration->shouldReceive('load')
            ->with(42)
            ->once()
            ->andReturn($config);

        $this->sizeGenerator->shouldReceive('generate')
            ->with($config)
            ->once()
            ->andReturn($mapData);
        $this->massCenterGenerator->shouldReceive('generate')
            ->with([], null, $mapData, $config)
            ->once();
        $this->asteroidRingGenerator->shouldReceive('generate')
            ->with($mapData, $config)
            ->once();
        $this->planetMoonGenerator->shouldReceive('generate')
            ->with($mapData, $config)
            ->once();

        $result = $this->subject->generate(42, [], null);

        $this->assertSame($mapData, $result);
    }

    public function testGenerateForAllSystemTypes(): void
    {
        $subject = new StarsystemGenerator(
            new LoadSystemConfiguration(),
            new SizeGenerator(),
            new MassCenterGenerator(),
            new AsteroidRingGenerator(),
            new PlanetMoonGenerator(new PlanetMoonProbabilities())
        );

        $types = $this->subject->getSupportedSystemTypes();

        foreach ($types as $type) {

            //skip template
            if ($type === 0) {
                continue;
            }

            $result = $subject->generate($type, [1], [1]);

            $this->assertTrue(count($result->getFieldData()) >= 49);
        }
    }
}
