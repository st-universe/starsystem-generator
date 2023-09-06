<?php

namespace Stu\StarsystemGenerator\Lib;

class Point implements PointInterface
{
    private int $x;
    private int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

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
}
