<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\AsteroidTypeEnum;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Exception\FieldAlreadyUsedException;
use Stu\StarsystemGenerator\Exception\HardBlockedFieldException;
use Stu\StarsystemGenerator\Lib\Field;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\SystemMapDataInterface;

// TODO unit tests
final class AsteroidRingGenerator implements AsteroidRingGeneratorInterface
{
    public const MINIMUM_WIDTH_PER_RING = 12;
    public const RING_POINTS_PER_GAP = 10;
    public const MAXIMUM_GAP_ANGLE = 45;

    private StuRandom $stuRandom;

    public function __construct(StuRandom $stuRandom)
    {
        $this->stuRandom = $stuRandom;
    }

    public function generate(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config,
        int $firstMassCenterWidth,
        int $secondMassCenterWidth
    ): void {

        if (!$config->hasAsteroids()) {
            return;
        }

        $ringRadiusPercentages = $this->getRingRadiusPercentages($mapData, $config, $firstMassCenterWidth, $secondMassCenterWidth);

        foreach ($ringRadiusPercentages as $radiusPercentage) {
            $this->createRing($radiusPercentage, $mapData);
        }
    }

    /**
     * @return array<int>
     */
    private function getRingRadiusPercentages(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config,
        int $firstMassCenterWidth,
        int $secondMassCenterWidth
    ): array {
        $overallMassCenterWidth = sqrt($firstMassCenterWidth)
            + sqrt($secondMassCenterWidth)
            + max($config->getMassCenterDistanceVertical(), $config->getMassCenterDistanceHorizontal());

        $leftWidth = $mapData->getWidth() - $overallMassCenterWidth;

        $ringCount = (int)floor($leftWidth / self::MINIMUM_WIDTH_PER_RING);

        $ringRadiusPercentages = [];

        $partialDistance = $leftWidth / 2 / ($ringCount + 1);

        for ($i = 1; $i <= $ringCount; $i++) {
            $ringRadiusPercentages[] = (int)(($partialDistance * $i + $overallMassCenterWidth / 2) / ($mapData->getWidth() / 2) * 100);
        }

        return $ringRadiusPercentages;
    }

    private function createRing(int $radiusPercentage, SystemMapDataInterface $mapData): void
    {
        $possibleLocations = $mapData->getAsteroidRing($radiusPercentage);

        $this->insertGaps($possibleLocations);

        $asteroidType = $this->getAsteroidType($radiusPercentage);

        $asteroidRingPoints = [];

        foreach ($possibleLocations as $point) {
            $fieldId = AsteroidTypeEnum::getFieldId(
                $asteroidType,
                AsteroidTypeEnum::ASTEROID_CATEGORIES[$this->stuRandom->rand(0, count(AsteroidTypeEnum::ASTEROID_CATEGORIES) - 1)]
            );

            try {
                $mapData->setField(new Field($point, $fieldId));
                $asteroidRingPoints[] = $point;
            } catch (FieldAlreadyUsedException | HardBlockedFieldException $e) {
                //nothing to do here
            }
        }

        foreach ($asteroidRingPoints as $point) {
            $mapData->blockField(
                $point,
                true,
                FieldTypeEnum::ASTEROID,
                BlockedFieldTypeEnum::HARD_BLOCK
            );
        }
    }

    /** @param array<int, PointInterface> $ringPoints */
    private function insertGaps(array &$ringPoints): void
    {
        $gapCount = count($ringPoints) / self::RING_POINTS_PER_GAP;

        //echo sprintf('gapCount: %d', $gapCount);

        for ($i = 0; $i < $gapCount; $i++) {
            $gapAngle = $this->stuRandom->rand(1, self::MAXIMUM_GAP_ANGLE, true);
            $gapStartAngle = $this->stuRandom->rand(0, 360);

            //echo sprintf('gapAngle: %d+%d', $gapStartAngle, $gapAngle);

            for ($angle = $gapStartAngle; $angle <= $gapStartAngle + $gapAngle; $angle++) {
                unset($ringPoints[$angle]);
            }
        }
    }

    private function getAsteroidType(int $radiusPercentage): int
    {
        if ($radiusPercentage > 66) {
            return AsteroidTypeEnum::ASTEROID_TYPE_ICE;
        }

        return AsteroidTypeEnum::ASTEROID_TYPES[$this->stuRandom->rand(0, count(AsteroidTypeEnum::ASTEROID_TYPES) - 1)];
    }
}
