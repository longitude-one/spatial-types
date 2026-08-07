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
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\MissingValueException;
use LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString as GeometricLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point as GeometricPoint;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory
 */
class FromIndexedArrayFactoryTest extends TestCase
{
    /**
     * Verifies that coordinate pairs are converted into a line string with the expected properties.
     */
    public function testCreateLineStringFromCoordinates(): void
    {
        $lineString = FromIndexedArrayFactory::createLineString([[0, 0], [1, 1]], 4326, FamilyEnum::GEOGRAPHY);

        static::assertCount(2, $lineString->getPoints());
        static::assertSame(4326, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $lineString->getFamily());
        static::assertFalse($lineString->isEmpty());
    }

    /**
     * Verifies that existing point objects are preserved when building a line string.
     */
    public function testCreateLineStringFromPoints(): void
    {
        $points = [new GeometricPoint(1, 2), new GeometricPoint(3, 4)];

        $lineString = FromIndexedArrayFactory::createLineString($points, 2154);

        static::assertSame($points, $lineString->getPoints());
        static::assertSame(2154, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOMETRY, $lineString->getFamily());
    }

    /**
     * Verifies that invalid line string elements raise an invalid value exception.
     */
    public function testCreateLineStringRejectsInvalidElement(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('The array must contain only objects implementing PointInterface or array of coordinates.');

        // @phpstan-ignore-next-line
        FromIndexedArrayFactory::createLineString(['not a point']);
    }

    /**
     * Verifies that an unsupported dimension raises an invalid dimension exception.
     */
    public function testCreateLineStringRejectsUnsupportedDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('Only the two-dimensions line-strings and the three-dimensions elevation line-strings are yet supported.');

        FromIndexedArrayFactory::createLineString([[0, 0]], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_M);
    }

    /**
     * Verifies that a point is created from indexed coordinates with the expected SRID and family.
     */
    public function testCreatePoint(): void
    {
        $point = FromIndexedArrayFactory::createPoint([1, 2], 2154, FamilyEnum::GEOGRAPHY);

        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(2154, $point->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
    }

    /**
     * Verifies that a two-dimensional point creation rejects arrays with an invalid number of elements.
     */
    public function testCreatePoint2dRejectsAnArrayWithAnInvalidDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('To create a two-dimensional point, your array shall contains exactly two elements.');

        FromIndexedArrayFactory::createPoint([1, 2, 3], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);
    }

    /**
     * Verifies that invalid point coordinates raise an invalid value exception.
     */
    public function testCreatePoint2dRejectsInvalidCoordinates(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Invalid coordinate value, got "invalid".');

        FromIndexedArrayFactory::createPoint(['invalid', 2], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y);
    }

    /**
     * Verify that a non-indexed array point creation will raise a missing argument exception.
     */
    public function testCreatePointRejectsAnArrayNonIndexed(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('The first coordinate must be stored at array index 0. Index 0 is missing.');

        // @phpstan-ignore-next-line
        FromIndexedArrayFactory::createPoint([
            'foo' => 0,
            'bar' => 1,
        ]);
    }

    /**
     * Verify that a non-indexed array point creation will raise a missing argument exception.
     */
    public function testCreatePointRejectsAnotherArrayNonIndexed(): void
    {
        self::expectException(MissingValueException::class);
        self::expectExceptionMessageIsOrContains('The second coordinate must be stored at array index 1. Index 1 is missing.');

        // @phpstan-ignore-next-line
        FromIndexedArrayFactory::createPoint([
            0 => 0,
            'foo' => 1,
        ]);
    }

    /**
     * Verifies that an unsupported point dimension raises an invalid dimension exception.
     */
    public function testCreatePointRejectsUnsupportedDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('Only the two-dimensions points and elevation point (3dZ) are yet supported.');

        FromIndexedArrayFactory::createPoint([1, 2, 3, 4], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z_M);
    }

    /**
     * Verifies that closed coordinate arrays are converted into a polygon.
     */
    public function testCreatePolygonFromCoordinates(): void
    {
        $polygon = FromIndexedArrayFactory::createPolygon([
            [[0, 0], [2, 0], [2, 2], [0, 0]],
            [[0.5, 0.5], [1, 0.5], [1, 1], [0.5, 0.5]],
        ], 4326, FamilyEnum::GEOGRAPHY);

        static::assertCount(2, $polygon->getRings());
        static::assertSame(4326, $polygon->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $polygon->getFamily());
    }

    /**
     * Verifies that existing closed line strings are preserved when building a polygon.
     */
    public function testCreatePolygonFromLineStrings(): void
    {
        $rings = [new GeometricLineString([[0, 0], [1, 0], [1, 1], [0, 0]], 2154)];

        $polygon = FromIndexedArrayFactory::createPolygon($rings, 2154);

        static::assertSame($rings, $polygon->getRings());
        static::assertSame(FamilyEnum::GEOMETRY, $polygon->getFamily());
    }

    /**
     * Verifies that a polygon only accepts closed line strings.
     */
    public function testCreatePolygonRejectsAnOpenLineString(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('The line string is not a ring.');

        FromIndexedArrayFactory::createPolygon([[[0, 0], [1, 1]]]);
    }

    /**
     * Verifies that a polygon rejects invalid ring elements.
     */
    public function testCreatePolygonRejectsInvalidElement(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('The array must contain only objects implementing LineStringInterface or array of coordinates.');

        // @phpstan-ignore-next-line
        FromIndexedArrayFactory::createPolygon(['not a line string']);
    }

    /**
     * Verifies that an unsupported polygon dimension raises an invalid dimension exception.
     */
    public function testCreatePolygonRejectsUnsupportedDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('Only the two-dimensions polygons are yet supported.');

        FromIndexedArrayFactory::createPolygon([], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z);
    }
}
