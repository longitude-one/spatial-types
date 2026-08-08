<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2024-2026
 * Copyright Longitude One 2024-2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Enum;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Enum\DimensionEnum
 */
class DimensionEnumTest extends TestCase
{
    /**
     * Check the coordinate layout described by every dimension.
     *
     * @param DimensionEnum $dimension      dimension to check
     * @param int           $expectedCount  expected coordinate count
     * @param bool          $hasM           expected M-coordinate presence
     * @param bool          $hasZ           expected Z-coordinate presence
     * @param null|int      $expectedMIndex M coordinate index
     * @param null|int      $expectedZIndex Z coordinate index
     */
    #[DataProvider('provideDimensions')]
    public function testCoordinateLayout(DimensionEnum $dimension, int $expectedCount, bool $hasM, bool $hasZ, ?int $expectedMIndex, ?int $expectedZIndex): void
    {
        static::assertSame($expectedCount, $dimension->coordinateCount());
        static::assertSame($hasM, $dimension->hasM());
        static::assertSame($hasZ, $dimension->hasZ());
        static::assertSame($expectedMIndex, $dimension->mIndex());
        static::assertSame($expectedZIndex, $dimension->zIndex());
    }

    /**
     * Provide every supported coordinate layout.
     *
     * @return \Generator<string, array{0: DimensionEnum, 1: int, 2: bool, 3: bool, 4: null|int, 5: null|int}, null, void>
     */
    public static function provideDimensions(): \Generator
    {
        yield 'XY' => [DimensionEnum::X_Y, 2, false, false, null, null];
        yield 'XYM' => [DimensionEnum::X_Y_M, 3, true, false, 2, null];
        yield 'XYZ' => [DimensionEnum::X_Y_Z, 3, false, true, null, 2];
        yield 'XYZM' => [DimensionEnum::X_Y_Z_M, 4, true, true, 3, 2];
    }
}
