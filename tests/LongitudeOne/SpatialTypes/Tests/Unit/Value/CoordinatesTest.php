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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Value\Coordinates
 */
class CoordinatesTest extends TestCase
{
    /**
     * Coordinates cannot carry only part of the ordinates required by their dimension.
     *
     * @param \Closure $operation invalid coordinate value construction
     */
    #[DataProvider('provideIncompatibleCoordinates')]
    public function testConstructorRejectsIncompatibleOrdinates(\Closure $operation): void
    {
        self::expectException(InvalidDimensionException::class);
        $operation();
    }

    /**
     * @return \Generator<string, array{0: \Closure}, null, void>
     */
    public static function provideIncompatibleCoordinates(): \Generator
    {
        yield 'XYM without measure' => [static fn () => new Coordinates(DimensionEnum::X_Y_M, 1, 2)];

        yield 'XYZ with measure instead of elevation' => [static fn () => new Coordinates(DimensionEnum::X_Y_Z, 1, 2, null, 3)];

        yield 'XYZM without measure' => [static fn () => new Coordinates(DimensionEnum::X_Y_Z_M, 1, 2, 3)];
    }

    /**
     * Verify that a coordinate value rejects ordinates incompatible with its dimension.
     */
    public function testConstructorRejectsUnexpectedElevationForTwoDimensionalCoordinates(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessage('do not match the XY coordinate dimension');

        new Coordinates(DimensionEnum::X_Y, 1, 2, 3);
    }

    /**
     * Missing ordinates are rejected on read as well as on update.
     *
     * @param \Closure $operation coordinate operation that requires an absent ordinate
     */
    #[DataProvider('provideOperationsRequiringAnAbsentOrdinate')]
    public function testOperationsRejectAbsentOrdinates(\Closure $operation): void
    {
        self::expectException(InvalidDimensionException::class);
        $operation();
    }

    /**
     * @return \Generator<string, array{0: \Closure}, null, void>
     */
    public static function provideOperationsRequiringAnAbsentOrdinate(): \Generator
    {
        yield 'read M from XY' => [static fn () => Coordinates::xy(1, 2)->getM()];

        yield 'read Z from XYM' => [static fn () => Coordinates::xym(1, 2, 3)->getZ()];

        yield 'replace M on XYZ' => [static fn () => Coordinates::xyz(1, 2, 3)->withM(4)];
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
     * Updating an ordinate supported by the dimension preserves every other ordinate.
     */
    public function testReplacingSupportedOrdinatesCreatesIndependentValues(): void
    {
        $xym = Coordinates::xym(1, 2, 3);
        $xymWithM = $xym->withM(4);
        static::assertNotSame($xym, $xymWithM);
        static::assertSame([1, 2, 3], $xym->toArray());
        static::assertSame([1, 2, 4], $xymWithM->toArray());

        $xyz = Coordinates::xyz(1, 2, 3);
        $xyzWithY = $xyz->withY(4);
        static::assertNotSame($xyz, $xyzWithY);
        static::assertSame([1, 2, 3], $xyz->toArray());
        static::assertSame([1, 4, 3], $xyzWithY->toArray());

        $xyzm = Coordinates::xyzm(1, 2, 3, 4);
        $xyzmWithZ = $xyzm->withZ(5);
        static::assertNotSame($xyzm, $xyzmWithZ);
        static::assertSame([1, 2, 3, 4], $xyzm->toArray());
        static::assertSame([1, 2, 5, 4], $xyzmWithZ->toArray());
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
