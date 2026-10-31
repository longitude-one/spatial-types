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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Internal\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable as GeographyPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable as GeometryPoint;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for issue #36.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPointImmutable
 */
class PartiallyNullXYPointTest extends TestCase
{
    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinate(): void
    {
        $point = new GeographyPoint(0, null, 4326);
        $coordinates = Coordinates::xy(0, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XY);
        $original = new GeographyPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, null], $point->toArray());
        static::assertSame([0, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinate(): void
    {
        $point = new GeographyPoint(0, 2, 4326);
        $coordinates = Coordinates::xy(0, 2);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XY);
        $original = new GeographyPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, 2], $point->toArray());
        static::assertSame([0, 2], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinate(): void
    {
        $point = new GeographyPoint(null, null, 4326);
        $coordinates = Coordinates::xy(null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XY);
        $original = new GeographyPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([], $point->toArray());
        static::assertSame([null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinate(): void
    {
        $point = new GeographyPoint(null, 2, 4326);
        $coordinates = Coordinates::xy(null, 2);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XY);
        $original = new GeographyPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([null, 2], $point->toArray());
        static::assertSame([null, 2], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinate(): void
    {
        $point = new GeometryPoint(0, null, 4326);
        $coordinates = Coordinates::xy(0, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XY);
        $original = new GeometryPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, null], $point->toArray());
        static::assertSame([0, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinate(): void
    {
        $point = new GeometryPoint(0, 2, 4326);
        $coordinates = Coordinates::xy(0, 2);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XY);
        $original = new GeometryPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, 2], $point->toArray());
        static::assertSame([0, 2], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinate(): void
    {
        $point = new GeometryPoint(null, null, 4326);
        $coordinates = Coordinates::xy(null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XY);
        $original = new GeometryPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([], $point->toArray());
        static::assertSame([null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinate(): void
    {
        $point = new GeometryPoint(null, 2, 4326);
        $coordinates = Coordinates::xy(null, 2);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XY);
        $original = new GeometryPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([null, 2], $point->toArray());
        static::assertSame([null, 2], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2], $original->toArray());
    }
}
