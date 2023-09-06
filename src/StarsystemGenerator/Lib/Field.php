<?php

namespace Stu\StarsystemGenerator\Lib;

class Field implements FieldInterface
{
    private PointInterface $point;
    private int $id;

    public function __construct(PointInterface $point, int $id)
    {
        $this->point = $point;
        $this->id = $id;
    }

    public function getPoint(): PointInterface
    {
        return $this->point;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
