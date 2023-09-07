<?php

namespace Stu\StarsystemGenerator\Lib;

interface PointInterface
{
    public function getX(): int;

    public function getY(): int;

    public function getLeft(): PointInterface;

    public function getRight(): PointInterface;

    public function __toString(): string;
}
