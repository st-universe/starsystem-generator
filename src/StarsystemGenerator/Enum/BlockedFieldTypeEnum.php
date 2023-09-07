<?php

declare(strict_types=1);

namespace Stu\StarsystemGenerator\Enum;

final class BlockedFieldTypeEnum
{
    public const NOT_BLOCKED = 0;
    public const SOFT_BLOCK = 1;
    public const EDGE_BLOCK = 2;
    public const HARD_BLOCK = 3;
}
