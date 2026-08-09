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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory;

use LongitudeOne\SpatialTypes\Enum\DimensionEnum;
use LongitudeOne\SpatialTypes\Enum\FamilyEnum;
use LongitudeOne\SpatialTypes\Enum\TypeEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Exception\SpatialTypeExceptionInterface;
use LongitudeOne\SpatialTypes\Factory\FactoryPoint;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Test the FactoryPoint class.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\FactoryPoint
 */
class FactoryPointTest extends TestCase
{
    /**
     * Test the factory with some good coordinates.
     */
    public function testFromCoordinates(): void
    {
        $point = FactoryPoint::fromCoordinates(1, 2);
        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertFalse($point->hasM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOMETRY, $point->getFamily());
        static::assertSame(TypeEnum::POINT, $point->getType());

        $point = FactoryPoint::fromCoordinates(42.1, 42.2, null, null, 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y);
        static::assertSame(42.1, $point->getX());
        static::assertSame(42.2, $point->getY());
        static::assertFalse($point->hasM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(TypeEnum::POINT, $point->getType());
    }

    /**
     * Test that a measure must be numeric.
     */
    public function testFromCoordinatesRejectsNonNumericMeasure(): void
    {
        self::expectException(\TypeError::class);

        $method = new \ReflectionMethod(FactoryPoint::class, 'fromCoordinates');
        $method->invoke(null, 1, 2, 3, json_decode('{}'), null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z_M);
    }

    /**
     * Test that zero-valued extra coordinates are not silently ignored.
     *
     * @param null|float|int $z The Z coordinate to test
     * @param null|float|int $m The M coordinate to test
     */
    #[DataProvider('provideZeroValuedExtraCoordinates')]
    public function testFromCoordinatesRejectsZeroValuedExtraCoordinates(float|int|null $z, float|int|null $m): void
    {
        self::expectException(InvalidDimensionException::class);

        FactoryPoint::fromCoordinates(1, 2, $z, $m);
    }

    /**
     * Provide zero-valued extra coordinates.
     *
     * @return \Generator<string, array{0: null|float|int, 1: null|float|int}, null, void>
     */
    public static function provideZeroValuedExtraCoordinates(): \Generator
    {
        yield 'Zero Z' => [0, null];

        yield 'Zero M' => [null, 0];
    }

    /**
     * Test the factory with some bad dimension.
     */
    public function testFromCoordinatesWithBadDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('The third and fourth dimensions are not supported for two-dimensions points. Did you miss the 7th parameter DimensionEnum?');
        FactoryPoint::fromCoordinates(1, 2, 3, 4);
    }

    /**
     * Test that a three-dimensional elevation point requires an elevation.
     */
    public function testFromCoordinatesWithMissingElevation(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('The third coordinate is missing.');

        FactoryPoint::fromCoordinates(1, 2, null, null, null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z);
    }

    /**
     * Test that an unsupported elevation and measure point reaches the unsupported dimension error.
     *
     * @param DimensionEnum $dimension the dimension to test
     */
    #[DataProvider('provideUnsupportedDimensions')]
    public function testFromCoordinatesWithUnsupportedDimension(DimensionEnum $dimension): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('Only two-dimension points, three-dimension measure points, and three-dimension elevation points are yet supported');

        FactoryPoint::fromCoordinates(1, 2, null, 3, null, FamilyEnum::GEOMETRY, $dimension);
    }

    /**
     * Provide unsupported dimensions.
     *
     * @return \Generator<string, array{0: DimensionEnum}, null, void>
     */
    public static function provideUnsupportedDimensions(): \Generator
    {
        yield 'Elevation and measure point' => [DimensionEnum::X_Y_Z_M];
    }

    /**
     * Test the factory with some good coordinates in an array.
     */
    public function testFromIndexedArray(): void
    {
        $point = FactoryPoint::fromIndexedArray([1, 2]);
        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertFalse($point->hasM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOMETRY, $point->getFamily());
        static::assertSame(TypeEnum::POINT, $point->getType());

        $point = FactoryPoint::fromIndexedArray([42.1, 42.2], 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y);
        static::assertSame(42.1, $point->getX());
        static::assertSame(42.2, $point->getY());
        static::assertFalse($point->hasM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(TypeEnum::POINT, $point->getType());
    }

    /**
     * Test the factory with an invalid value.
     */
    public function testFromIndexedArrayInvalidCoordinate(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Invalid coordinate value, got "invalid".');
        FactoryPoint::fromIndexedArray(['invalid', 2]);
    }

    /**
     * Test the factory with the first coordinate missing in the array.
     */
    public function testFromIndexedArrayMissingFirstCoordinate(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('The first coordinate of array is missing.');
        FactoryPoint::fromIndexedArray([null, 2]);
    }

    /**
     * Test that a required fourth coordinate is checked before creating an unsupported point dimension.
     */
    public function testFromIndexedArrayMissingFourthCoordinate(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('The fourth coordinate of array is missing.');

        FactoryPoint::fromIndexedArray([1, 2, 3], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z_M);
    }

    /**
     * Test that a required third coordinate is checked before creating an unsupported point dimension.
     */
    public function testFromIndexedArrayMissingMeasureCoordinate(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('The third coordinate of array is missing.');

        FactoryPoint::fromIndexedArray([1, 2], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_M);
    }

    /**
     * Test the factory with some bad values in an array.
     */
    public function testFromIndexedArrayMissingSecondCoordinate(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('The second coordinate of array is missing.');
        FactoryPoint::fromIndexedArray([1, null]);
    }

    /**
     * Test that a required third coordinate is checked before creating an unsupported point dimension.
     */
    public function testFromIndexedArrayMissingThirdCoordinate(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('The third coordinate of array is missing.');

        FactoryPoint::fromIndexedArray([1, 2], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z);
    }

    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint

    /**
     * Test the factory with some bad values in an array.
     *
     * @param int[]                                       $values            The values to test
     * @param class-string<SpatialTypeExceptionInterface> $exceptedException The expected exception
     * @param string                                      $expectedMessage   The expected message
     */
    #[DataProvider('provideBadValues')]
    public function testFromIndexedArrayWithBadValues(array $values, string $exceptedException, string $expectedMessage): void
    {
        self::expectException($exceptedException);
        self::expectExceptionMessageIsOrContains($expectedMessage);
        FactoryPoint::fromIndexedArray($values);
    }

    /**
     * Provide bad values to test the factory.
     *
     * @return \Generator<string, array{0: int[], 1: class-string<SpatialTypeExceptionInterface>, 2: string}, null, void>
     */
    public static function provideBadValues(): \Generator
    {
        yield 'Empty array' => [
            [],
            MissingValueException::class,
            'The first coordinate of array is missing.',
        ];

        yield 'One value' => [
            [1],
            MissingValueException::class,
            'The second coordinate of array is missing.',
        ];

        yield 'Three values' => [
            [1, 2, 3],
            InvalidDimensionException::class,
            'The array must contain exactly 2 coordinates to create a XY point.',
        ];

        yield 'Four values' => [
            [1, 2, 3, 4],
            InvalidDimensionException::class,
            'The array must contain exactly 2 coordinates to create a XY point.',
        ];

        yield 'Five values' => [
            [1, 2, 3, 4, 5],
            InvalidDimensionException::class,
            'The array must contain exactly 2 coordinates to create a XY point.',
        ];
    }

    /**
     * Test the factory with some non-compatible dimensions.
     */
    public function testFromIndexedArrayWithDifferentDimensions(): void
    {
        static::markTestSkipped('The third dimensions have not been created yet.');
        $point = FactoryPoint::fromIndexedArray([1, 2, 3], 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y_Z);
        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertFalse($point->hasM());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(TypeEnum::POINT, $point->getType());

        $point = FactoryPoint::fromIndexedArray([1, 2, 4], 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y_M);
        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(4, $point->getM());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(TypeEnum::POINT, $point->getType());

        $point = FactoryPoint::fromIndexedArray([1, 2, 3, 4], 4326, FamilyEnum::GEOGRAPHY, DimensionEnum::X_Y_Z_M);
        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(4, $point->getZ());
        static::assertFalse($point->hasZ());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(TypeEnum::POINT, $point->getType());
    }
}
