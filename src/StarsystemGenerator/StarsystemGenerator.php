<?php

namespace Stu\StarsystemGenerator;

use DirectoryIterator;
use Generator;
use Stu\StarsystemGenerator\Component\AsteroidRingGenerator;
use Stu\StarsystemGenerator\Component\AsteroidRingGeneratorInterface;
use Stu\StarsystemGenerator\Component\LoadSystemConfiguration;
use Stu\StarsystemGenerator\Component\LoadSystemConfigurationInterface;
use Stu\StarsystemGenerator\Component\MassCenterGenerator;
use Stu\StarsystemGenerator\Component\MassCenterGeneratorInterface;
use Stu\StarsystemGenerator\Component\MoonPlacement;
use Stu\StarsystemGenerator\Component\PlanetMoonGenerator;
use Stu\StarsystemGenerator\Component\PlanetMoonGeneratorInterface;
use Stu\StarsystemGenerator\Component\PlanetPlacement;
use Stu\StarsystemGenerator\Component\PlanetRingPlacement;
use Stu\StarsystemGenerator\Component\SizeGenerator;
use Stu\StarsystemGenerator\Component\SizeGeneratorInterface;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilities;
use Stu\StarsystemGenerator\Lib\StuRandom;

final class StarsystemGenerator implements StarsystemGeneratorInterface
{
    private LoadSystemConfigurationInterface $loadSystemTypeConfiguration;
    private SizeGeneratorInterface $sizeGenerator;
    private MassCenterGeneratorInterface $massCenterGenerator;
    private AsteroidRingGeneratorInterface $asteroidRingGenerator;
    private PlanetMoonGeneratorInterface $planetMoonGenerator;

    public function __construct(
        LoadSystemConfigurationInterface $loadSystemTypeConfiguration,
        SizeGeneratorInterface $sizeGenerator,
        MassCenterGeneratorInterface $massCenterGenerator,
        AsteroidRingGeneratorInterface $asteroidRingGenerator,
        PlanetMoonGeneratorInterface $planetMoonGenerator
    ) {
        $this->loadSystemTypeConfiguration = $loadSystemTypeConfiguration;
        $this->sizeGenerator = $sizeGenerator;
        $this->massCenterGenerator = $massCenterGenerator;
        $this->asteroidRingGenerator = $asteroidRingGenerator;
        $this->planetMoonGenerator = $planetMoonGenerator;
    }

    public static function getInstance(): StarsystemGeneratorInterface
    {
        $planetMoonProbabilities = new PlanetMoonProbabilities();
        $planetRingPlacement = new PlanetRingPlacement();
        $stuRandom = new StuRandom();

        return new StarsystemGenerator(
            new LoadSystemConfiguration(),
            new SizeGenerator($stuRandom),
            new MassCenterGenerator(),
            new AsteroidRingGenerator($stuRandom),
            new PlanetMoonGenerator(
                new PlanetPlacement($planetMoonProbabilities, $planetRingPlacement, $stuRandom),
                new MoonPlacement($planetMoonProbabilities, $stuRandom),
                $stuRandom
            )
        );
    }

    /**
     * - array aufbauen, size random
     *      - array in Object, als Param in die einzelnen Generatoren rein
     *      - 7 bis 27
     * - anhand size -> stern / binär etc.
     *      - von field_id in stu_map die beiden rechten Stellen
     *          abschneiden um zur ID von stu_system_types zu kommen
     *      - typen in stu_system_types
     *      - gerade anzahl: 2x2, binäre
     *      - ungerade: 1x1, 3x3, binäre
     *      - bonusFelder random
     * - 0,1,2 ringe (abhängig von size)
     *      - dichte, abhängig vom letzten Feld davor
     *      - position
     * - planeten verteilen
     * - monde verteilen
     * - Vorschau rendern
     * - Name raussuchen stu_system_name
     */

    public function generate(int $systemType, array $firstMassCenterFields, ?array $secondMassCenterFields): SystemMapDataInterface
    {
        $config = $this->loadSystemTypeConfiguration->load($systemType);

        $mapData = $this->sizeGenerator->generate($config, $secondMassCenterFields !== null);

        $this->massCenterGenerator->generate(
            $firstMassCenterFields,
            $secondMassCenterFields,
            $mapData,
            $config
        );

        $this->asteroidRingGenerator->generate(
            $mapData,
            $config,
            count($firstMassCenterFields),
            $secondMassCenterFields === null ? 0 : count($secondMassCenterFields)
        );
        $this->planetMoonGenerator->generate($mapData, $config);

        return $mapData;
    }

    public function getSupportedSystemTypes(): Generator
    {
        $list = new DirectoryIterator(__DIR__ . '/Config/Data');

        foreach ($list as $file) {
            if (!$file->isDir()) {
                yield (int) str_replace('.php', '', $file->getFilename());
            }
        }
    }
}
