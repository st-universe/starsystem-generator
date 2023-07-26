<?php

namespace Stu\StarsystemGenerator;

final class SystemMapData implements SystemMapDataInterface
{
    /** @var array<int, array<int, int>> */
    private array $fieldData;

    public function __construct(int $width, int $height)
    {
        $this->fieldData = array_fill(1, $height, array_fill(1, $width, 0));
    }

    public function setFieldId(int $x, int $y, int $fieldId): SystemMapDataInterface
    {
        $this->fieldData[$y][$x] = $fieldId;

        return $this;
    }

    public function toString(): string
    {
        return implode(
            "\n",
            array_map(
                fn (array $row): string => implode(
                    ",",
                    $row
                ),
                $this->fieldData
            )
        );
    }
}
