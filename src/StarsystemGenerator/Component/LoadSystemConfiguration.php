<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Exception\StarsystemGeneratorFileMissingException;

final class LoadSystemConfiguration implements LoadSystemConfigurationInterface
{
    public function load(int $systemType): SystemConfigurationInterface
    {
        $fileName = sprintf(
            '%s/../Config/Data/%d.php',
            __DIR__,
            $systemType
        );
        if (!file_exists($fileName)) {
            throw new StarsystemGeneratorFileMissingException('Systemgenerator description file missing for systemType ' . $systemType);
        }
        $requireResult = require $fileName;

        if (!$requireResult instanceof SystemConfigurationInterface) {
            throw new StarsystemGeneratorFileMissingException('Error loading Systemgenerator description file for systemType ' . $systemType);
        }

        return $requireResult;
    }
}
