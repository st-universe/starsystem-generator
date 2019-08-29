<?php

use Stu\StarsystemGenerator\StarsystemGenerator;

require_once __DIR__.'/../vendor/autoload.php';

$starId = 901;

$systemGenerator = new StarsystemGenerator();
$systemGenerator->generate($starId);

