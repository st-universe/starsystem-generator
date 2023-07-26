<?php

namespace Stu\StarsystemGenerator;

use Mockery\MockInterface;
use Stu\StarsystemGenerator\Component\SizeGenerator;
use Stu\StarsystemGenerator\Component\SizeGeneratorInterface;
use Stu\StarsystemGenerator\Exception\StarsystemGeneratorFileMissingException;

final class StarsystemGeneratorTest extends StuTestCase
{
    /** @var MockInterface|SizeGeneratorInterface  */
    private MockInterface $sizeGenerator;

    private StarsystemGeneratorInterface $subject;

    public function setUp(): void
    {
        $this->sizeGenerator = static::mock(SizeGeneratorInterface::class);

        $this->subject = new StarsystemGenerator(
            $this->sizeGenerator
        );
    }

    public function testGenerateExpectExceptionWhenNoConfigFoundForSystemType(): void
    {
        static::expectExceptionMessage('Systemgenerator description file missing for systemType 42');
        static::expectException(StarsystemGeneratorFileMissingException::class);

        $this->subject->generate(42);
    }

    public function testGenerateExpectExceptionWhen(): void
    {
        static::expectExceptionMessage('Error loading Systemgenerator description file for systemType 0');
        static::expectException(StarsystemGeneratorFileMissingException::class);

        $this->subject->generate(0);
    }

    public function testGenerateForAllSystemTypes(): void
    {
        $subject = new StarsystemGenerator(
            new SizeGenerator()
        );

        $types = $this->subject->getSupportedSystemTypes();

        foreach ($types as $type) {

            //skip template
            if ($type === 0) {
                continue;
            }

            $result = $subject->generate($type);

            $this->assertTrue(count($result->getFieldData()) >= 49);
        }
    }
}
