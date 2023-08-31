<?php

namespace Stu\StarsystemGenerator;

use Generator;

interface StarsystemGeneratorInterface
{
    /**
     * @param array<int, int> $firstMassCenterFields
     * @param array<int, int>|null $secondMassCenterFields
     */
    public function generate(
        int $systemType,
        array $firstMassCenterFields,
        ?array $secondMassCenterFields
    ): SystemMapDataInterface;

    /**
     * @return Generator<int>
     */
    public function getSupportedSystemTypes(): Generator;
}
