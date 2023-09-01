<?php

namespace Stu\StarsystemGenerator\Config;

use Stu\StarsystemGenerator\StuTestCase;

final class PlanetMoonProbabilitiesTest extends StuTestCase
{
    private PlanetMoonProbabilitiesInterface $subject;

    public function setUp(): void
    {
        $this->subject = new PlanetMoonProbabilities();
    }

    public function testPickRandomFieldId(): void
    {
        $values = [];

        foreach (range(1, 2485) as $i) {
            $random = $this->subject->pickRandomFieldId([], null, true);

            if (!array_key_exists($random, $values)) {
                $values[$random] = 1;
            } else {
                $values[$random] = $values[$random] + 1;
            }
        }

        $this->assertFalse(empty($values));

        //echo print_r($values, true);
    }
}
