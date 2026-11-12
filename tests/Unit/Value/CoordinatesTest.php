<?php

/*
 * This file is part of the LongitudeOne Spatial project.
 *
 * Copyright (c) 2026 Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * See LICENSE for copyright and license information.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Value;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
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
     * @return \Generator<string, array{0: \Closure}, null, void>
     */
    public static function provideIncompatibleCoordinates(): \Generator
    {
        yield 'XYZ with measure instead of elevation' => [static fn () => new Coordinates(CoordinateDimensionEnum::XYZ, 1, 2, null, 3)];
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
     * Coordinates reject supplied ordinates outside their dimension.
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
     * Verify that a coordinate value rejects ordinates incompatible with its dimension.
     */
    public function testConstructorRejectsUnexpectedElevationForTwoDimensionalCoordinates(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessage('do not match the XY coordinate dimension');

        new Coordinates(CoordinateDimensionEnum::XY, 1, 2, 3);
    }

    /** Null replacement preserves the layout and the original coordinate value. */
    public function testElevationCoordinatesAcceptNullReplacements(): void
    {
        $coordinates = Coordinates::xyz(1, 2, 3);
        $replacement = $coordinates->withX(null)->withY(null)->withZ(null);

        self::assertNotSame($coordinates, $replacement);
        self::assertSame([1, 2, 3], $coordinates->toArray());
        self::assertSame([null, null, null], $replacement->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $replacement->dimension);
    }

    /** Null replacement preserves the layout and the original coordinate value. */
    public function testFourDimensionalCoordinatesAcceptNullReplacements(): void
    {
        $coordinates = Coordinates::xyzm(1, 2, 3, 4);
        $replacement = $coordinates->withX(null)->withY(null)->withZ(null)->withM(null);

        self::assertNotSame($coordinates, $replacement);
        self::assertSame([1, 2, 3, 4], $coordinates->toArray());
        self::assertSame([null, null, null, null], $replacement->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $replacement->dimension);
    }

    /** Null replacement preserves the layout and the original coordinate value. */
    public function testMeasureCoordinatesAcceptNullReplacements(): void
    {
        $coordinates = Coordinates::xym(1, 2, 3);
        $replacement = $coordinates->withX(null)->withY(null)->withM(null);

        self::assertNotSame($coordinates, $replacement);
        self::assertSame([1, 2, 3], $coordinates->toArray());
        self::assertSame([null, null, null], $replacement->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $replacement->dimension);
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

        self::assertNotSame($coordinates, $updatedCoordinates);
        self::assertSame([1, 2, 3, 4], $coordinates->toArray());
        self::assertSame([5, 2, 3, 4], $updatedCoordinates->toArray());
    }

    /**
     * Updating an ordinate supported by the dimension preserves every other ordinate.
     */
    public function testReplacingSupportedOrdinatesCreatesIndependentValues(): void
    {
        $xym = Coordinates::xym(1, 2, 3);
        $xymWithM = $xym->withM(4);
        self::assertNotSame($xym, $xymWithM);
        self::assertSame([1, 2, 3], $xym->toArray());
        self::assertSame([1, 2, 4], $xymWithM->toArray());

        $xyz = Coordinates::xyz(1, 2, 3);
        $xyzWithY = $xyz->withY(4);
        self::assertNotSame($xyz, $xyzWithY);
        self::assertSame([1, 2, 3], $xyz->toArray());
        self::assertSame([1, 4, 3], $xyzWithY->toArray());

        $xyzm = Coordinates::xyzm(1, 2, 3, 4);
        $xyzmWithZ = $xyzm->withZ(5);
        self::assertNotSame($xyzm, $xyzmWithZ);
        self::assertSame([1, 2, 3, 4], $xyzm->toArray());
        self::assertSame([1, 2, 5, 4], $xyzmWithZ->toArray());
    }

    /**
     * Verify that coordinate values retain their dimension-specific ordinate order.
     */
    public function testToArrayUsesCoordinateDimensionOrder(): void
    {
        self::assertSame([1, 2], Coordinates::xy(1, 2)->toArray());
        self::assertSame([1, 2, 3], Coordinates::xym(1, 2, 3)->toArray());
        self::assertSame([1, 2, 3], Coordinates::xyz(1, 2, 3)->toArray());
        self::assertSame([1, 2, 3, 4], Coordinates::xyzm(1, 2, 3, 4)->toArray());
    }

    /** Null replacement preserves the layout and the original coordinate value. */
    public function testTwoDimensionalCoordinatesAcceptNullReplacements(): void
    {
        $coordinates = Coordinates::xy(1, 2);
        $replacement = $coordinates->withX(null)->withY(null);

        self::assertNotSame($coordinates, $replacement);
        self::assertSame([1, 2], $coordinates->toArray());
        self::assertSame([null, null], $replacement->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $replacement->dimension);
    }
}
