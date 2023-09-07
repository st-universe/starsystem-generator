<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Config\PlanetMoonProbabilitiesInterface;
use Stu\StarsystemGenerator\Config\PlanetRadius;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\Exception\DisplayNotSuitableForMoonException;
use Stu\StarsystemGenerator\Exception\EdgeBlockedFieldException;
use Stu\StarsystemGenerator\Exception\FieldAlreadyUsedException;
use Stu\StarsystemGenerator\Exception\HardBlockedFieldException;
use Stu\StarsystemGenerator\Exception\MassCenterPerimeterBlockedFieldException;
use Stu\StarsystemGenerator\Exception\UnknownFieldIndexException;
use Stu\StarsystemGenerator\Lib\Field;
use Stu\StarsystemGenerator\Lib\PlanetDisplayInterface;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\Lib\StuRandom;
use Stu\StarsystemGenerator\SystemMapDataInterface;

//TODO unit tests
final class MoonPlacement implements MoonPlacementInterface
{
    public const MAX_RETRIES_PER_DISPLAY = 20;
    public const MAX_RETRIES_FOR_RANDOM_LOCATION = 20;

    private PlanetMoonProbabilitiesInterface $planetMoonProbabilities;
    private StuRandom $stuRandom;

    public function __construct(
        PlanetMoonProbabilitiesInterface $planetMoonProbabilities,
        StuRandom $stuRandom
    ) {
        $this->planetMoonProbabilities = $planetMoonProbabilities;
        $this->stuRandom = $stuRandom;
    }

    public function placeMoon(
        int &$moonAmount,
        int &$planetAmount,
        ?PlanetDisplayInterface $planetDisplay,
        SystemMapDataInterface $mapData,
        SystemConfigurationInterface $config
    ): void {

        $maxTries = self::MAX_RETRIES_PER_DISPLAY;

        $randomMoonFieldId = $this->planetMoonProbabilities->pickRandomFieldId(
            [],
            $config->getProbabilities(FieldTypeEnum::MOON),
            $config->getPropabilityBlacklist(FieldTypeEnum::MOON),
            true
        );

        while ($maxTries > 0) {

            try {
                $randomLocation = $planetDisplay === null
                    ? $this->getRandomLocationForMoon($mapData, $randomMoonFieldId)
                    : $planetDisplay->getRandomPoint($this->stuRandom);

                if ($randomLocation !== null) {
                    $mapData->setField(new Field($randomLocation, $randomMoonFieldId), BlockedFieldTypeEnum::SOFT_BLOCK);
                    $mapData->blockField($randomLocation, false, FieldTypeEnum::MOON, BlockedFieldTypeEnum::HARD_BLOCK);
                    $mapData->addIdentifier(
                        $randomLocation,
                        $this->getMoonIdentifier($planetDisplay, $planetAmount)
                    );

                    break;
                }
            } catch (
                UnknownFieldIndexException | EdgeBlockedFieldException
                | HardBlockedFieldException | FieldAlreadyUsedException
                | MassCenterPerimeterBlockedFieldException $e
            ) {
                //nothing to do here
            }

            $maxTries--;
        }

        if ($maxTries === 0) {
            throw new DisplayNotSuitableForMoonException('could not place the moon');
        }

        $moonAmount++;
    }

    private function getMoonIdentifier(?PlanetDisplayInterface $planetDisplay, int &$planetAmount): string
    {
        if ($planetDisplay === null) {
            $planetAmount++;

            return (string)$planetAmount;
        } else {
            return $planetDisplay->getMoonIdentifier();
        }
    }

    private function getRandomLocationForMoon(
        SystemMapDataInterface $mapData,
        int $moonFieldId
    ): ?PointInterface {

        $planetDisplay = null;

        $maxTries = self::MAX_RETRIES_FOR_RANDOM_LOCATION;
        while ($maxTries > 0 && $planetDisplay === null) {
            $moonRadiusPercentage = PlanetRadius::getRandomPlanetRadiusPercentage($moonFieldId, $this->stuRandom, true);

            $planetDisplay = $mapData->getPlanetDisplay($moonRadiusPercentage, 0);

            $maxTries--;
        }

        if ($planetDisplay === null) {
            return null;
        }

        return $planetDisplay->getFirstPoint();
    }
}
