<?php

namespace Stu\StarsystemGenerator\Component;

use RuntimeException;
use Stu\StarsystemGenerator\Config\PlanetMoonProbabilitiesInterface;
use Stu\StarsystemGenerator\Config\PlanetMoonRange;
use Stu\StarsystemGenerator\Config\PlanetRadius;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class PlanetMoonGenerator implements PlanetMoonGeneratorInterface
{
    private PlanetMoonProbabilitiesInterface $planetMoonProbabilities;

    public function __construct(PlanetMoonProbabilitiesInterface $planetMoonProbabilities)
    {
        $this->planetMoonProbabilities = $planetMoonProbabilities;
    }

    public function generate(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void {

        $planetAmount = $this->getPlanetAmount($mapData, $config);
        $moonAmount = $this->getMoonAmount($mapData, $config);

        $planetLocations = [];

        while ($planetAmount > 0) {
            $planetLocations[] = $this->placePlanet($planetAmount, $moonAmount, $mapData, $config);
        }
        while ($moonAmount > 0) {
            $this->placeMoon($moonAmount);
        }
    }

    /**
     * @return array{0: int, 1:int}
     */
    private function placePlanet(int &$planetAmount, int &$moonAmount, SystemMapDataInterface $mapData, SystemConfigurationInterface $config): array
    {
        $customProbabilities = $config->getProbabilities(FieldTypeEnum::PLANET);
        $randomPlanetFieldId = $this->planetMoonProbabilities->pickRandomFieldId(empty($customProbabilities) ? null : $customProbabilities);

        $planetDisplay = $mapData->getPlanetDisplay(
            PlanetRadius::getRandomPlanetRadiusPercentage($randomPlanetFieldId),
            PlanetMoonRange::getPlanetMoonRange($randomPlanetFieldId)
        );

        if ($planetDisplay === null) {
            throw new RuntimeException('no place found for planet');
        }

        $planetAmount++;

        [$centerX, $centerY] = $this->getCenterCoordinate($planetDisplay);

        $mapData->setFieldId($centerX, $centerY, $randomPlanetFieldId, FieldTypeEnum::PLANET);

        return [$centerX, $centerY];
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

    /** @param null|array{0: int, 1:int} $planetLocation */
    private function placeMoon(int &$moonAmount, ?array $planetLocation = null): void
    {
    }

    private function getPlanetAmount(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): int {
        if (!$config->hasPlanets()) {
            return 0;
        }

        $maxFromConfig = $config->getMaxPlanets();
        $fieldAmount = $mapData->getFieldAmount();

        $planetAmount = (int)($fieldAmount / random_int(36, 81));

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
        $fieldAmount = $mapData->getFieldAmount();

        $planetAmount = (int)($fieldAmount / random_int(19, 62));

        return min($maxFromConfig, $planetAmount);
    }
}
