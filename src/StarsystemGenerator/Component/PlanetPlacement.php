<?php

namespace Stu\StarsystemGenerator\Component;

use RuntimeException;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilitiesInterface;
use Stu\StarsystemGenerator\Config\PlanetMoonRange;
use Stu\StarsystemGenerator\Config\PlanetRadius;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Lib\Field;
use Stu\StarsystemGenerator\Lib\Point;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class PlanetPlacement implements PlanetPlacementInterface
{
    public const MAX_TRIED_PLANET_TYPES = 20;
    public const MAX_RETRIES_PER_PLANET_TYPE = 5;

    private PlanetMoonProbabilitiesInterface $planetMoonProbabilities;
    private StuRandom $stuRandom;

    public function __construct(
        PlanetMoonProbabilitiesInterface $planetMoonProbabilities,
        StuRandom $stuRandom
    ) {
        $this->planetMoonProbabilities = $planetMoonProbabilities;
        $this->stuRandom = $stuRandom;
    }

    public function placePlanet(int &$planetAmount, SystemMapDataInterface $mapData, SystemConfigurationInterface $config): array
    {
        $planetDisplay = null;

        $maxTries = self::MAX_TRIED_PLANET_TYPES;

        $triedPlanetFieldIds = [];

        while ($maxTries > 0) {
            $randomPlanetFieldId = $this->planetMoonProbabilities->pickRandomFieldId(
                $triedPlanetFieldIds,
                $config->getProbabilities(FieldTypeEnum::PLANET),
                $config->getPropabilityBlacklist(FieldTypeEnum::PLANET)
            );
            $triedPlanetFieldIds[] = $randomPlanetFieldId;

            $planetDisplay = $this->tryToFindPlanetDisplay($mapData, $randomPlanetFieldId);

            if ($planetDisplay !== null) {
                break;
            }

            $maxTries--;
        }

        if ($planetDisplay === null) {
            $this->dumpBothDisplays($mapData);
            throw new RuntimeException(sprintf('could not place any of %d colony classes', self::MAX_TRIED_PLANET_TYPES));
        }

        $planetAmount--;

        $centerPoint = $this->getCenterCoordinate($planetDisplay);

        try {
            $mapData->setField(new Field($centerPoint, $randomPlanetFieldId));
        } catch (RuntimeException $e) {
            $this->dumpBothDisplays($mapData);

            throw $e;
        }
        $mapData->blockField($centerPoint, true, FieldTypeEnum::PLANET, BlockedFieldTypeEnum::SOFT_BLOCK);

        //hard block fields left and right if ring planet
        if ((int)($randomPlanetFieldId / 100) === 3) {
            $mapData->blockField($centerPoint->getLeft(), false, null, BlockedFieldTypeEnum::HARD_BLOCK);
            $mapData->blockField($centerPoint->getRight(), false, null, BlockedFieldTypeEnum::HARD_BLOCK);
        }

        return $planetDisplay;
    }

    /**
     * @return null|array<int, PointInterface>
     */
    private function tryToFindPlanetDisplay(
        SystemMapDataInterface $mapData,
        int $randomPlanetFieldId
    ): ?array {

        $planetMoonRange = PlanetMoonRange::getPlanetMoonRange($randomPlanetFieldId);

        $planetDisplay = null;

        $maxTries = self::MAX_RETRIES_PER_PLANET_TYPE;
        while ($maxTries > 0) {
            $planetRadiusPercentage = PlanetRadius::getRandomPlanetRadiusPercentage($randomPlanetFieldId, $this->stuRandom);

            $planetDisplay = $mapData->getPlanetDisplay(
                $planetRadiusPercentage,
                $planetMoonRange
            );

            if ($planetDisplay !== null) {
                break;
            }

            $maxTries--;
        }

        return $planetDisplay;
    }

    /** 
     * @param array<int, PointInterface> $fields
     */
    private function getCenterCoordinate(array $fields): PointInterface
    {
        $firstPoint = current($fields);
        $lastPoint = end($fields);

        if ($firstPoint === false || $lastPoint === false) {
            throw new RuntimeException('this should not happen');
        }

        return new Point(($firstPoint->getX() + $lastPoint->getX()) / 2,
            ($firstPoint->getY() + $lastPoint->getY()) / 2
        );
    }

    private function dumpBothDisplays(SystemMapDataInterface $mapData): void
    {
        echo "FAIL";
        echo "<br>";
        echo $mapData->toString(true);
        echo "<br>";
        echo $mapData->toString(true, true);
    }
}
