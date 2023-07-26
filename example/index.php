<?php

use Stu\StarsystemGenerator\Component\SizeGenerator;
use Stu\StarsystemGenerator\StarsystemGenerator;

require_once __DIR__ . '/../vendor/autoload.php';

$systemType = 1002;

$systemGenerator = new StarsystemGenerator(new SizeGenerator());

$systemMapData = $systemGenerator->generate($systemType);

echo $systemMapData->toString(true);
