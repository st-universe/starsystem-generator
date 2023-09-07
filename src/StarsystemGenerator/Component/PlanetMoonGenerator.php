<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Exception\DisplayNotSuitableForMoonException;
use Stu\StarsystemGenerator\Exception\MoonMaximumReachedException;
use Stu\StarsystemGenerator\Exception\NoSuitablePlanetTypeFoundException;
use Stu\StarsystemGenerator\Exception\PlanetMaximumReachedException;
use Stu\StarsystemGenerator\Lib\PlanetDisplayInterface;
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

        $maxPlanets = $this->getPlanetAmount($mapData, $config);
        $maxMoons = $this->getMoonAmount($mapData, $config);

        $planetAmount = 0;
        $moonAmount = 0;

        $planetDisplays = [];

        try {
            while ($planetAmount < $maxPlanets) {
                $planetDisplays[] = $this->planetPlacement->placePlanet($planetAmount, $mapData, $config);
            }
        } catch (NoSuitablePlanetTypeFoundException | PlanetMaximumReachedException $e) {
            //echo 'stoppedPlanetPLacement';
        }
        while ($moonAmount < $maxMoons) {
            //echo $moonAmount;

            try {
                $this->placeMoon(
                    $moonAmount,
                    $planetAmount,
                    $planetDisplays,
                    $mapData,
                    $config
                );
            } catch (MoonMaximumReachedException $e) {
                break;
            }
        }
    }

    /** 
     * @param array<int, PlanetDisplayInterface> $planetDisplays
     */
    private function placeMoon(
        int &$moonAmount,
        int &$planetAmount,
        array $planetDisplays,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config,
    ): void {

        $maxTries = self::MAXIMUM_MOON_PLACEMENT_TRIES;

        while ($maxTries > 0) {
            try {
                $randomDisplayIndex = $this->stuRandom->rand(0, count($planetDisplays));
                $isTrabant =  array_key_exists($randomDisplayIndex, $planetDisplays);
                $this->moonPlacement->placeMoon(
                    $moonAmount,
                    $planetAmount,
                    $isTrabant ? $planetDisplays[$randomDisplayIndex] : null,
                    $mapData,
                    $config
                );
                break;
            } catch (DisplayNotSuitableForMoonException $e) {
                // nothing to do here
            }

            $maxTries--;
        }

        // if placement impossible, stop moon placement
        if ($maxTries === 0) {
            throw new MoonMaximumReachedException('no more moons placeable');
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
