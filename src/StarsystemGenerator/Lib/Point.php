<?php

namespace Stu\StarsystemGenerator\Lib;

class Point implements PointInterface
{
    public function __construct(private int $x, private int $y) {}

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getLeft(): PointInterface
    {
        return new Point($this->getX() - 1, $this->getY());
    }

    public function getRight(): PointInterface
    {
        return new Point($this->getX() + 1, $this->getY());
    }

    public function __toString(): string
    {
        return sprintf('(%d, %d)', $this->getX(), $this->getY());
    }
}
