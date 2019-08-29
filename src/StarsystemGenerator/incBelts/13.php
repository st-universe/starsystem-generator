<?php

use Stu\StarsystemGenerator\StarsystemGenerator;

$bphases = 0;

$thin = 703;
$thick = 704;

$density = 40;
$degradation = 2;
$fragmentation = 200;
$invert = 0;
$isthick = 0;

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
