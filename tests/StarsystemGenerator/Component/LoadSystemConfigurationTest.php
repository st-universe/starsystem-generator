<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Exception\StarsystemGeneratorFileMissingException;
use Stu\StarsystemGenerator\StuTestCase;

final class LoadSystemConfigurationTest extends StuTestCase
{
    private LoadSystemConfigurationInterface $subject;

    public function setUp(): void
    {
        $this->subject = new LoadSystemConfiguration();
    }

    public function testGenerateExpectExceptionWhenNoConfigFoundForSystemType(): void
    {
        static::expectExceptionMessage('Systemgenerator description file missing for systemType 42');
        static::expectException(StarsystemGeneratorFileMissingException::class);

        $this->subject->load(42);
    }

    public function testGenerateExpectExceptionWhenConfigDataErroneous(): void
    {
        static::expectExceptionMessage('Error loading Systemgenerator description file for systemType 0');
        static::expectException(StarsystemGeneratorFileMissingException::class);

        $this->subject->load(0);
    }
}
