<?php

namespace Stu\StarsystemGenerator;

use Mockery\MockInterface;
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

    public function testGenerate(): void
    {
        static::expectExceptionMessage('userId 42 tried to navigate from 41|41 to invalid position 42|42');
        static::expectException(StarsystemGeneratorFileMissingException::class);

        $this->subject->generate(42);
    }
}
