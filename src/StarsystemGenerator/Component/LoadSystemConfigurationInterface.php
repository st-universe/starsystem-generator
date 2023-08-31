<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Exception\StarsystemGeneratorFileMissingException;

interface LoadSystemConfigurationInterface
{
    /**
     * @throws StarsystemGeneratorFileMissingException
     */
    public function load(int $systemType): SystemConfigurationInterface;
}
