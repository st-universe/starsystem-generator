<?php

use Stu\StarsystemGenerator\StarsystemGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$systemType = 1002;

$systemGenerator = StarsystemGenerator::getInstance();

$massCenterFields = [];
for ($i = 0; $i < pow(10, 2); $i++) {
    $massCenterFields[] = 5;
}

$systemMapData = $systemGenerator->generate($systemType, [5, 5, 5, 5], [6, 6, 6, 6, 6, 6, 6, 6, 6]);

echo "<br> MAP <br>";
echo $systemMapData->toString(true);

echo "<br>";

echo "BLOCKED_FIELDS <br>";
echo $systemMapData->toString(true, true);

echo "<br>";

echo "IDENTIFIERS <br>";
$systemMapData->printIdentifiers();
