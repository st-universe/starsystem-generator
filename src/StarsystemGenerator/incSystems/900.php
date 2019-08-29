<?php

use Stu\StarsystemGenerator\StarsystemGenerator;

$data[StarsystemGenerator::NAME] = "Oranger Zwerg + Oranger Zwerg";

$data[StarsystemGenerator::RADIUS] = rand(10, 12);

$zone[1] = 25;
$zone[2] = 35;
$zone[3] = 30;
$zone[4] = 10;

$stars = 0;

$star[$stars][StarsystemGenerator::TYPE] = 900;
$star[$stars][StarsystemGenerator::WIDTH] = 2;
$star[$stars][StarsystemGenerator::OBORDER] = 1;
$star[$stars][StarsystemGenerator::IBORDER] = 1;
$stars++;

$belts = $this->draw(array(0 => 30, 1 => 50, 2 => 20));
if ($belts > 1) {
    $data[StarsystemGenerator::RADIUS] += 2;
}

$belt[1] = $this->draw(array(11 => 25, 12 => 25, 13 => 25, 14 => 25));
$belt[2] = $this->draw(array(11 => 25, 12 => 25, 13 => 25, 14 => 25));

$data[StarsystemGenerator::PLANETS] = $data[StarsystemGenerator::RADIUS] - 3;
if ($belts == 0) {
    $data[StarsystemGenerator::PLANETS] += 2;
}

return [$star, $stars, $data, $zone, $belt, $belts];