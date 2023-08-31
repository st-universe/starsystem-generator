<?php

use Stu\StarsystemGenerator\Component\AsteroidRingGenerator;
use Stu\StarsystemGenerator\Component\LoadSystemConfiguration;
use Stu\StarsystemGenerator\Component\MassCenterGenerator;
use Stu\StarsystemGenerator\Component\PlanetMoonGenerator;
use Stu\StarsystemGenerator\Component\SizeGenerator;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilities;
use Stu\StarsystemGenerator\StarsystemGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$systemType = 1002;

$systemGenerator = new StarsystemGenerator(
    new LoadSystemConfiguration(),
    new SizeGenerator(),
    new MassCenterGenerator(),
    new AsteroidRingGenerator(),
    new PlanetMoonGenerator(new PlanetMoonProbabilities())
);

$systemMapData = $systemGenerator->generate($systemType, [1], [3, 4, 5, 6]);

echo $systemMapData->toString(true);
