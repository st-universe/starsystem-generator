<?php

namespace Stu\StarsystemGenerator\Component;

use InvalidArgumentException;
use Stu\StarsystemGenerator\Config\SystemConfigurationInterface;
use Stu\StarsystemGenerator\Enum\BlockedFieldTypeEnum;
use Stu\StarsystemGenerator\Enum\FieldTypeEnum;
use Stu\StarsystemGenerator\StuTestCase;
use Stu\StarsystemGenerator\SystemMapDataInterface;

final class MassCenterGeneratorTest extends StuTestCase
{
    private MassCenterGeneratorInterface $subject;

    public function setUp(): void
    {
        $this->subject = new MassCenterGenerator();
    }

    public static function provideFieldArraysNotSquareData()
    {
        return [
            [[1, 1], null, 2],
            [[1, 1, 1], null, 3],
            [[1, 1, 1, 1, 1], null, 5],
            [[1], [1, 1, 1, 1, 1, 1], 6],
            [[1], [1, 1, 1, 1, 1, 1, 1], 7],
            [[1], [1, 1, 1, 1, 1, 1, 1, 1], 8]
        ];
    }

    /**
     * @dataProvider provideFieldArraysNotSquareData
     */
    public function testGenerateExpectExceptionWhenFieldArraysNotSquare(
        array $firstMassCenterFields,
        ?array $secondMassCenterFields,
        int $fieldCount
    ): void {
        static::expectExceptionMessage(sprintf('fieldCount %d is illegal value', $fieldCount));
        static::expectException(InvalidArgumentException::class);

        $mapData = $this->mock(SystemMapDataInterface::class);
        $config = $this->mock(SystemConfigurationInterface::class);

        $this->subject->generate(
            $firstMassCenterFields,
            $secondMassCenterFields,
            $mapData,
            $config
        );
    }
    public static function provideCorrectPositionData()
    {
        return [
            //firstFields
            //secondFields    ?distX   ?distY     sysW    sysH
            //expectedMap
            [
                [5],
                null,           null,    null,      3,      3,
                [
                    [0, 0, 0],
                    [0, 5, 0],
                    [0, 0, 0]
                ]
            ],
            [
                [9, 8, 7, 6],
                null,           null,    null,      4,      4,
                [
                    [0, 0, 0, 0],
                    [0, 9, 8, 0],
                    [0, 7, 6, 0],
                    [0, 0, 0, 0]
                ]
            ],
            [
                [1],
                null,           null,    null,      2,      2,
                [
                    [1, 0],
                    [0, 0]
                ]
            ],
            [
                [1],
                [2],           1,    1,      5,      5,
                [
                    [0, 0, 0, 0, 0],
                    [0, 1, 0, 0, 0],
                    [0, 0, 0, 0, 0],
                    [0, 0, 0, 2, 0],
                    [0, 0, 0, 0, 0]
                ]
            ],
            [
                [1],
                [2],           1,    2,      5,      5,
                [
                    [0, 1, 0, 0, 0],
                    [0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0],
                    [0, 0, 0, 2, 0],
                    [0, 0, 0, 0, 0]
                ]
            ],
            [
                [5, 6, 7, 8],
                [2],           1,    1,      6,      6,
                [
                    [0, 0, 0, 0, 0, 0],
                    [0, 5, 6, 0, 0, 0],
                    [0, 7, 8, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 2, 0],
                    [0, 0, 0, 0, 0, 0]
                ]
            ],
            [
                [1, 2, 3, 4, 5, 6, 7, 8, 9],
                [4, 3, 2, 1],           3,    3,      10,      10,
                [
                    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    [0, 1, 2, 3, 0, 0, 0, 0, 0, 0],
                    [0, 4, 5, 6, 0, 0, 0, 0, 0, 0],
                    [0, 7, 8, 9, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0, 0, 4, 3, 0],
                    [0, 0, 0, 0, 0, 0, 0, 2, 1, 0],
                    [0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
                ]
            ],
            [
                [1],
                [5, 6, 7, 8],           1,    1,      6,      6,
                [
                    [0, 0, 0, 0, 0, 0],
                    [0, 1, 0, 0, 0, 0],
                    [0, 0, 0, 0, 0, 0],
                    [0, 0, 0, 5, 6, 0],
                    [0, 0, 0, 7, 8, 0],
                    [0, 0, 0, 0, 0, 0]
                ]
            ],
        ];
    }

    /**
     * @dataProvider provideCorrectPositionData
     */
    public function testGenerateExpectCorrectPositionInMapData(
        array $firstMassCenterFields,
        ?array $secondMassCenterFields,
        ?int $distanceX,
        ?int $distanceY,
        int $systemWidth,
        int $systemHeight,
        array $expectedMap
    ): void {

        $mapData = $this->mock(SystemMapDataInterface::class);
        $config = $this->mock(SystemConfigurationInterface::class);

        $mapData->shouldReceive('getWidth')
            ->withNoArgs()
            ->andReturn($systemWidth);
        $mapData->shouldReceive('getHeight')
            ->withNoArgs()
            ->andReturn($systemHeight);

        if ($distanceX !== null) {
            $config->shouldReceive('getMassCenterDistanceHorizontal')
                ->withNoArgs()
                ->once()
                ->andReturn($distanceX);
        }
        if ($distanceY !== null) {
            $config->shouldReceive('getMassCenterDistanceVertical')
                ->withNoArgs()
                ->once()
                ->andReturn($distanceY);
        }

        foreach ($expectedMap as $row => $values) {
            foreach ($values as $column => $value) {
                if ($value !== 0) {
                    $mapData->shouldReceive('setFieldId')
                        ->with($column + 1, $row + 1, $value, FieldTypeEnum::MASS_CENTER)
                        ->once();
                    $mapData->shouldReceive('blockField')
                        ->with($column + 1, $row + 1, true, FieldTypeEnum::MASS_CENTER, BlockedFieldTypeEnum::HARD_BLOCK)
                        ->once();
                }
            }
        }

        $this->subject->generate(
            $firstMassCenterFields,
            $secondMassCenterFields,
            $mapData,
            $config
        );
    }

    /**
     *   $config->shouldReceive('getMassCenterDistanceHorizontal')
            ->withNoArgs()
            ->once()
            ->andReturn($distanceX);
        $config->shouldReceive('getMassCenterDistanceVertical')
            ->withNoArgs()
            ->once()
            ->andReturn($distanceY);
     */
}
