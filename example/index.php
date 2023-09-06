<?php

use Stu\StarsystemGenerator\StarsystemGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$systemType = 1002;

$systemGenerator = StarsystemGenerator::getInstance();

$systemMapData = $systemGenerator->generate($systemType, [5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5], [6, 6, 6, 6, 6, 6, 6, 6, 6]);

echo "<br> MAP <br>";
echo $systemMapData->toString(true);

echo "<br>";

echo "BLOCKED_FIELDS <br>";
echo $systemMapData->toString(true, true);
