<?php

use Stu\StarsystemGenerator\StarsystemGenerator;

$bphases = 0;

$thin = 701;
$thick = 702;

$density = 120;
$degradation = 2;
$fragmentation = 5;
$invert = rand(0, 1);
$isthick = 1;

$bphase[$bphases][StarsystemGenerator::MODE] = "normal";
$bphase[$bphases][StarsystemGenerator::DESCRIPTION] = "Asteroid inner";
$bphase[$bphases][StarsystemGenerator::NUM] = 0;
$bphase[$bphases][StarsystemGenerator::FROM] = 0;
$bphase[$bphases][StarsystemGenerator::TO] = 0;
$bphase[$bphases][StarsystemGenerator::ADJACENT] = 0;
$bphase[$bphases][StarsystemGenerator::NOADJACENT] = 0;
$bphase[$bphases][StarsystemGenerator::NOADJACENTLIMIT] = 0;
$bphase[$bphases][StarsystemGenerator::FRAGMENTATION] = 20;
$bphases++;

$bphase[$bphases][StarsystemGenerator::MODE] = "normal";
$bphase[$bphases][StarsystemGenerator::DESCRIPTION] = "Asteroid inner";
$bphase[$bphases][StarsystemGenerator::NUM] = 0;
$bphase[$bphases][StarsystemGenerator::FROM] = 0;
$bphase[$bphases][StarsystemGenerator::TO] = 0;
$bphase[$bphases][StarsystemGenerator::ADJACENT] = 0;
$bphase[$bphases][StarsystemGenerator::NOADJACENT] = 0;
$bphase[$bphases][StarsystemGenerator::NOADJACENTLIMIT] = 0;
$bphase[$bphases][StarsystemGenerator::FRAGMENTATION] = 20;
$bphases++;

return [$bphases, $bphase, $thick, $thin, $density, $degradation, $fragmentation, $invert, $isthick];
