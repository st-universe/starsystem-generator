<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Exception\DisplayNotSuitableForMoonException;
use Stu\StarsystemGenerator\Exception\NoSuitablePlanetTypeFoundException;
use Stu\StarsystemGenerator\Exception\PlanetMaximumReachedException;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class PlanetMoonGenerator implements PlanetMoonGeneratorInterface
{
    public const MAXIMUM_MOON_PLACEMENT_TRIES = 20;

    private PlanetPlacementInterface $planetPlacement;

    private MoonPlacementInterface $moonPlacement;

    private StuRandom $stuRandom;

    public function __construct(
        PlanetPlacementInterface $planetPlacement,
        MoonPlacementInterface $moonPlacement,
        StuRandom $stuRandom
    ) {
        $this->planetPlacement = $planetPlacement;
        $this->moonPlacement = $moonPlacement;
        $this->stuRandom = $stuRandom;
    }

    public function generate(
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config,
    ): void {

        $planetAmount = $this->getPlanetAmount($mapData, $config);

        $moonAmount = $this->getMoonAmount($mapData, $config);

        $planetDisplays = [];

        try {
            while ($planetAmount > 0) {
                $planetDisplays[] = $this->planetPlacement->placePlanet($planetAmount, $mapData, $config);
            }
        } catch (NoSuitablePlanetTypeFoundException | PlanetMaximumReachedException $e) {
            //echo 'stoppedPlanetPLacement';
        }
        while ($moonAmount > 0) {
            //echo $moonAmount;
            $this->placeMoon($moonAmount, $planetDisplays, $mapData, $config);
        }
    }

    /** 
     * @param array<int, array<int, PointInterface>> $planetDisplays
     */
    private function placeMoon(
        int &$moonAmount,
        array $planetDisplays,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config,
    ): void {

        $maxTries = self::MAXIMUM_MOON_PLACEMENT_TRIES;

        while ($maxTries > 0 && $moonAmount > 0) {
            try {
                $randomDisplayIndex = $this->stuRandom->rand(0, count($planetDisplays));
                $isTrabant =  array_key_exists($randomDisplayIndex, $planetDisplays);
                $this->moonPlacement->placeMoon($moonAmount, $isTrabant ? $planetDisplays[$randomDisplayIndex] : null, $mapData, $config);
                break;
            } catch (DisplayNotSuitableForMoonException $e) {
                // nothing to do here
            }

            $maxTries--;
        }

        // if placement impossible, stop moon placement
        if ($maxTries === 0) {
            $moonAmount = 0;
        }
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
}
