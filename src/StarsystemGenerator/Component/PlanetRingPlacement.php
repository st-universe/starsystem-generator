<?php

namespace Stu\StarsystemGenerator\Component;

use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Lib\Field;
use Stu\StarsystemGenerator\Lib\PointInterface;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class PlanetRingPlacement implements PlanetRingPlacementInterface
{
    public function addPlanetRing(int $planetFieldId, PointInterface $planetLocation, SystemMapDataInterface $mapData): void
    {
        $leftRingFieldId = $planetFieldId * 10 + 1;
        $rightRingFieldId = $planetFieldId * 10 + 2;

        $leftRingPoint = $planetLocation->getLeft();
        $rightRingPoint = $planetLocation->getRight();

        $mapData->setField(new Field($leftRingPoint, $leftRingFieldId), BlockedFieldTypeEnum::MASS_CENTER_PERIMETER_BLOCK);
        $mapData->setField(new Field($rightRingPoint, $rightRingFieldId), BlockedFieldTypeEnum::MASS_CENTER_PERIMETER_BLOCK);

        $mapData->blockField($leftRingPoint, false, null, BlockedFieldTypeEnum::HARD_BLOCK);
        $mapData->blockField($rightRingPoint, false, null, BlockedFieldTypeEnum::HARD_BLOCK);
    }
}
