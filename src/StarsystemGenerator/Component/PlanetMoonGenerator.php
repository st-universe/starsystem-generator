<?php

namespace Stu\StarsystemGenerator\Component;

use RuntimeException;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilitiesInterface;
use Stu\StarsystemGenerator\Config\PlanetMoonRange;
use Stu\StarsystemGenerator\Config\PlanetRadius;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class PlanetMoonGenerator implements PlanetMoonGeneratorInterface
{
    private PlanetMoonProbabilitiesInterface $planetMoonProbabilities;
    private StuRandom $stuRandom;

    public function __construct(
        PlanetMoonProbabilitiesInterface $planetMoonProbabilities,
        StuRandom $stuRandom
    ) {
        $this->planetMoonProbabilities = $planetMoonProbabilities;
        $this->stuRandom = $stuRandom;
    }

    public function generate(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config,
    ): void {

        $planetAmount = $this->getPlanetAmount($mapData, $config);

        //echo sprintf(" planetAmount: %d<br>", $planetAmount);

        $moonAmount = $this->getMoonAmount($mapData, $config);

        $planetDisplays = [];

        while ($planetAmount > 0) {
            $planetDisplays[] = $this->placePlanet($planetAmount, $mapData, $config);
        }
        while ($moonAmount > 0) {
            $this->placeMoon($moonAmount, $planetDisplays);
        }
    }

    /**
     * @return array<int, array{0:int, 1:int}>
     */
    private function placePlanet(int &$planetAmount, SystemMapDataInterface $mapData, SystemConfigurationInterface $config): array
    {
        $customProbabilities = $config->getProbabilities(FieldTypeEnum::PLANET);

        $planetDisplay = null;

        $maxTries = 20;

        $triedPlanetFieldIds = [];

        while ($maxTries > 0) {
            $randomPlanetFieldId = $this->planetMoonProbabilities->pickRandomFieldId($triedPlanetFieldIds, empty($customProbabilities) ? null : $customProbabilities);
            $triedPlanetFieldIds[] = $randomPlanetFieldId;

            $planetDisplay = $this->tryToFindPlanetDisplay($mapData, $randomPlanetFieldId);

            if ($planetDisplay !== null) {
                break;
            }

            $maxTries--;
        }

        if ($planetDisplay === null) {
            $this->dumpBothDisplays($mapData);
            throw new RuntimeException('could not place any of 20 colony classes');
        }

        $planetAmount--;

        [$centerX, $centerY] = $this->getCenterCoordinate($planetDisplay);

        try {
            $mapData->setFieldId($centerX, $centerY, $randomPlanetFieldId, FieldTypeEnum::PLANET);
        } catch (RuntimeException $e) {
            $this->dumpBothDisplays($mapData);

            throw $e;
        }
        $mapData->blockField($centerX, $centerY, true, FieldTypeEnum::PLANET, BlockedFieldTypeEnum::SOFT_BLOCK);

        //hard block fields left and right if ring planet
        if ((int)($randomPlanetFieldId / 100) === 3) {
            $mapData->blockField($centerX - 1, $centerY, false, null, BlockedFieldTypeEnum::HARD_BLOCK);
            $mapData->blockField($centerX + 1, $centerY, false, null, BlockedFieldTypeEnum::HARD_BLOCK);
        }

        return $planetDisplay;
    }

    /**
     * @return null|array<int, array{0:int, 1:int}>
     */
    private function tryToFindPlanetDisplay(
        SystemMapDataInterface $mapData,
        int $randomPlanetFieldId
    ): ?array {

        $planetRadiusPercentage = PlanetRadius::getRandomPlanetRadiusPercentage($randomPlanetFieldId, $this->stuRandom);
        $planetMoonRange = PlanetMoonRange::getPlanetMoonRange($randomPlanetFieldId);

        //echo sprintf('planetRadiusPercentage: %d, planetMoonRange: %d ', $planetRadiusPercentage, $planetMoonRange);

        $planetDisplay = $mapData->getPlanetDisplay(
            $planetRadiusPercentage,
            $planetMoonRange
        );

        if ($planetDisplay === null) {
            //echo sprintf("no display found for planet type: %d <br>", $randomPlanetFieldId);
        }

        return $planetDisplay;
    }

    /** 
     * @param array<int, array{0: int, 1: int}> $fields
     * @return array{0: int, 1:int}
     */
    private function getCenterCoordinate(array $fields): array
    {
        $firstField = current($fields);
        $lastField = end($fields);

        if ($firstField === false || $lastField === false) {
            throw new RuntimeException('this should not happen');
        }

        [$minX, $minY] = $firstField;
        [$maxX, $maxY] = $lastField;

        return [($minX + $maxX) / 2, ($minY + $maxY) / 2];
    }

    /** 
     * @param array<int, array<int, array{0:int, 1:int}>> $planetDisplays
     */
    private function placeMoon(int &$moonAmount, array $planetDisplays): void
    {
        $moonAmount--;
    }

    private function getPlanetAmount(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): int {
        if (!$config->hasPlanets()) {
            return 0;
        }

        $maxFromConfig = $config->getMaxPlanets();
        $planetAmount = $mapData->getRandomPlanetAmount($this->stuRandom);

        return min($maxFromConfig, $planetAmount);
    }

    private function getMoonAmount(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): int {
        if (!$config->hasMoons()) {
            return 0;
        }

        $maxFromConfig = $config->getMaxMoons();
        $moonAmount = $mapData->getRandomMoonAmount($this->stuRandom);

        return min($maxFromConfig, $moonAmount);
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
