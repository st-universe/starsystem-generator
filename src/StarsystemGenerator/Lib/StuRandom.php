<?php

namespace Stu\StarsystemGenerator\Lib;

/**
 * This class adds the possibility to inject a random generator
 */
class StuRandom
{
    public function rand(int $min, int $max, bool $useStandardNormalDistribution = false): int
    {
        if ($useStandardNormalDistribution) {
            return $this->generateRandomValueStandardNormalDistribution($min, $max);
        }

        return random_int($min, $max);
    }

    private function generateRandomValueStandardNormalDistribution(int $min, int $max): int
    {
        $mean = (int)(($min + $max) / 2); // MW
        $stdDeviation = (int) ($mean / 2.5); // FWHM

        do {
            $value = random_int($min, $max);
            $probability = exp(-0.5 * (($value - $mean) / $stdDeviation) ** 2); // normal distribution
            $randomProbability = random_int(0, mt_getrandmax()) / mt_getrandmax();

            if ($randomProbability <= $probability) {
                return $value;
            }
        } while (true);
    }
}
