<?php

use Stu\StarsystemGenerator\Component\AsteroidRingGenerator;
use Stu\StarsystemGenerator\Component\LoadSystemConfiguration;
use Stu\StarsystemGenerator\Component\MassCenterGenerator;
use Stu\StarsystemGenerator\Component\PlanetMoonGenerator;
use Stu\StarsystemGenerator\Component\SizeGenerator;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilities;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\StarsystemGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$systemType = 1002;

$stuRandom = new StuRandom();

$systemGenerator = new StarsystemGenerator(
    new LoadSystemConfiguration(),
    new SizeGenerator($stuRandom),
    new MassCenterGenerator(),
    new AsteroidRingGenerator(),
    new PlanetMoonGenerator(new PlanetMoonProbabilities(), $stuRandom)
);

$systemMapData = $systemGenerator->generate($systemType, [1], [3, 4, 5, 6]);

echo "<br> MAP <br>";
echo $systemMapData->toString(true);

echo "<br>";

echo "BLOCKED_FIELDS <br>";
echo $systemMapData->toString(true, true);
