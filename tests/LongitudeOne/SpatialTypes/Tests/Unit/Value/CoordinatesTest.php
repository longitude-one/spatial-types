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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Value;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Value\Coordinates
 */
class CoordinatesTest extends TestCase
{
    /**
     * Verify that a coordinate value rejects ordinates incompatible with its dimension.
     */
    public function testConstructorRejectsIncompatibleOrdinates(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessage('do not match the XY coordinate dimension');

        new Coordinates(DimensionEnum::X_Y, 1, 2, 3);
    }

    /**
     * Verify that an absent ordinate cannot be updated.
     */
    public function testReplacingElevationRejectsTwoDimensionalCoordinates(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessage('has no Z ordinate');

        Coordinates::xy(1, 2)->withZ(3);
    }

    /**
     * Verify that a coordinate update returns a separate value and preserves other ordinates.
     */
    public function testReplacingFirstOrdinateCreatesDistinctCoordinateValue(): void
    {
        $coordinates = Coordinates::xyzm(1, 2, 3, 4);
        $updatedCoordinates = $coordinates->withX(5);

        static::assertNotSame($coordinates, $updatedCoordinates);
        static::assertSame([1, 2, 3, 4], $coordinates->toArray());
        static::assertSame([5, 2, 3, 4], $updatedCoordinates->toArray());
    }

    /**
     * Verify that coordinate values retain their dimension-specific ordinate order.
     */
    public function testToArrayUsesCoordinateDimensionOrder(): void
    {
        static::assertSame([1, 2], Coordinates::xy(1, 2)->toArray());
        static::assertSame([1, 2, 3], Coordinates::xym(1, 2, 3)->toArray());
        static::assertSame([1, 2, 3], Coordinates::xyz(1, 2, 3)->toArray());
        static::assertSame([1, 2, 3, 4], Coordinates::xyzm(1, 2, 3, 4)->toArray());
    }
}
