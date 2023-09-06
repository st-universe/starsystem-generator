<?php

namespace Stu\StarsystemGenerator\Lib;

interface FieldInterface
{
    public function getPoint(): PointInterface;

    public function getId(): int;
}
