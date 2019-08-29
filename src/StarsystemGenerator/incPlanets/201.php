<?php

use Stu\StarsystemGenerator\StarsystemGenerator;

$moons = $this->draw(array(0 => 30, 1 => 40, 2 => 20, 3 => 10));

$mphases = 0;

$mphase[$mphases][StarsystemGenerator::MODE] = "nocluster";
$mphase[$mphases][StarsystemGenerator::DESCRIPTION] = "Mond base";
$mphase[$mphases][StarsystemGenerator::NUM] = $this->randround(8 * $mooncount / 12);
$mphase[$mphases][StarsystemGenerator::FROM] = array("0" => "60");
$mphase[$mphases][StarsystemGenerator::TO] = array("0" => "430");
$mphase[$mphases][StarsystemGenerator::ADJACENT] = 0;
$mphase[$mphases][StarsystemGenerator::NOADJACENT] = 0;
$mphase[$mphases][StarsystemGenerator::NOADJACENTLIMIT] = 0;
$mphase[$mphases][StarsystemGenerator::FRAGMENTATION] = 0;
$mphases++;

$mphase[$mphases][StarsystemGenerator::MODE] = "nocluster";
$mphase[$mphases][StarsystemGenerator::DESCRIPTION] = "Mond add";
$mphase[$mphases][StarsystemGenerator::NUM] = $this->randround(4 * $mooncount / 12);
$mphase[$mphases][StarsystemGenerator::FROM] = array("0" => "60");
$mphase[$mphases][StarsystemGenerator::TO] = array("0" => "403");
$mphase[$mphases][StarsystemGenerator::ADJACENT] = 0;
$mphase[$mphases][StarsystemGenerator::NOADJACENT] = 0;
$mphase[$mphases][StarsystemGenerator::NOADJACENTLIMIT] = 0;
$mphase[$mphases][StarsystemGenerator::FRAGMENTATION] = 0;
$mphases++;

return [$moons, $mphases, $mphase];