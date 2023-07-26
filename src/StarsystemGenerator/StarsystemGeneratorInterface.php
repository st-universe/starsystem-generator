<?php

namespace Stu\StarsystemGenerator;

use Generator;

interface StarsystemGeneratorInterface
{
    public function generate(int $systemType);

    /**
     * @return Generator<int>
     */
    public function getSupportedSystemTypes(): Generator;
}
