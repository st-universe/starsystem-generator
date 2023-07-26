<?php

namespace Stu\StarsystemGenerator;

interface SystemMapDataInterface
{
    public function setFieldId(int $x, int $y, int $fieldId): SystemMapDataInterface;

    /** @return array<int, array<int, int>> */
    public function getFieldData(): array;

    public function toString(bool $doPrint = false): string;
}
