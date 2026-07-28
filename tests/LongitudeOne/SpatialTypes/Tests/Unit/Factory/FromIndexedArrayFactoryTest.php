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
use LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Types\Geography\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Geometry\Point as GeometricPoint;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory
 */
class FromIndexedArrayFactoryTest extends TestCase
{
    public function testCreateGeographicPoint2D(): void
    {
        $point = FromIndexedArrayFactory::createGeographicPoint2D([2.35, 48.85], 4326);

        static::assertSame(2.35, $point->getX());
        static::assertSame(48.85, $point->getY());
        static::assertSame(4326, $point->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
    }

    public function testCreateGeometricPoint2D(): void
    {
        $point = FromIndexedArrayFactory::createGeometricPoint2D([1, 2]);

        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getSrid());
        static::assertSame(FamilyEnum::GEOMETRY, $point->getFamily());
    }

    public function testCreatePoint(): void
    {
        $point = FromIndexedArrayFactory::createPoint([1, 2], 2154, FamilyEnum::GEOGRAPHY);

        static::assertSame(1, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(2154, $point->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $point->getFamily());
    }

    public function testCreatePointRejectsUnsupportedDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('Only the two-dimensions points are yet supported.');

        FromIndexedArrayFactory::createPoint([1, 2, 3], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_Z);
    }

    public function testCreatePoint2DRejectsAnArrayWithAnInvalidDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('To create a two-dimensional point, your array shall contains exactly two elements.');

        FromIndexedArrayFactory::createPoint2D([1, 2, 3]);
    }

    public function testCreatePoint2DRejectsInvalidCoordinates(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('Invalid coordinate value, got "invalid".');

        FromIndexedArrayFactory::createPoint2D(['invalid', 2]);
    }

    public function testCreateLineStringFromCoordinates(): void
    {
        $lineString = FromIndexedArrayFactory::createLineString([[0, 0], [1, 1]], 4326, FamilyEnum::GEOGRAPHY);

        static::assertCount(2, $lineString->getPoints());
        static::assertSame(4326, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOGRAPHY, $lineString->getFamily());
        static::assertFalse($lineString->isEmpty());
    }

    public function testCreateLineStringFromPoints(): void
    {
        $points = [new GeometricPoint(1, 2), new GeometricPoint(3, 4)];

        $lineString = FromIndexedArrayFactory::createLineString($points, 2154);

        static::assertSame($points, $lineString->getPoints());
        static::assertSame(2154, $lineString->getSrid());
        static::assertSame(FamilyEnum::GEOMETRY, $lineString->getFamily());
    }

    public function testCreateLineStringRejectsInvalidElement(): void
    {
        self::expectException(InvalidValueException::class);
        self::expectExceptionMessageIsOrContains('The array must contain only objects implementing PointInterface or array of coordinates.');

        FromIndexedArrayFactory::createLineString(['not a point']);
    }

    public function testCreateLineStringRejectsUnsupportedDimension(): void
    {
        self::expectException(InvalidDimensionException::class);
        self::expectExceptionMessageIsOrContains('Only the two-dimensions points are yet supported.');

        FromIndexedArrayFactory::createLineString([[0, 0]], null, FamilyEnum::GEOMETRY, DimensionEnum::X_Y_M);
    }
}
