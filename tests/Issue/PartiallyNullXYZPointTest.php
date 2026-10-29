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
use LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable as GeographyPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable as GeometryPoint;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for issue #36.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPointImmutable
 */
class PartiallyNullXYZPointTest extends TestCase
{
    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinateElevation(): void
    {
        $point = new GeographyPoint(0, null, 3, 4326);
        $coordinates = Coordinates::xyz(0, null, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, 3], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZ);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, 3], $point->toArray());
        static::assertSame([0, null, 3], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinateNullElevation(): void
    {
        $point = new GeographyPoint(0, null, null, 4326);
        $coordinates = Coordinates::xyz(0, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZ);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, null], $point->toArray());
        static::assertSame([0, null, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinateElevation(): void
    {
        $point = new GeographyPoint(0, 2, 3, 4326);
        $coordinates = Coordinates::xyz(0, 2, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, 3], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZ);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, 3], $point->toArray());
        static::assertSame([0, 2, 3], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinateNullElevation(): void
    {
        $point = new GeographyPoint(0, 2, null, 4326);
        $coordinates = Coordinates::xyz(0, 2, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZ);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, null], $point->toArray());
        static::assertSame([0, 2, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinateElevation(): void
    {
        $point = new GeographyPoint(null, null, 3, 4326);
        $coordinates = Coordinates::xyz(null, null, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, 3], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZ);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, null, 3], $point->toArray());
        static::assertSame([null, null, 3], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinateNullElevation(): void
    {
        $point = new GeographyPoint(null, null, null, 4326);
        $coordinates = Coordinates::xyz(null, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZ);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertTrue($point->isEmpty());
        static::assertSame([], $point->toArray());
        static::assertSame([null, null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinateElevation(): void
    {
        $point = new GeographyPoint(null, 2, 3, 4326);
        $coordinates = Coordinates::xyz(null, 2, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, 3], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZ);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, 3], $point->toArray());
        static::assertSame([null, 2, 3], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinateNullElevation(): void
    {
        $point = new GeographyPoint(null, 2, null, 4326);
        $coordinates = Coordinates::xyz(null, 2, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZ);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, null], $point->toArray());
        static::assertSame([null, 2, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinateElevation(): void
    {
        $point = new GeometryPoint(0, null, 3, 4326);
        $coordinates = Coordinates::xyz(0, null, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, 3], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZ);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, 3], $point->toArray());
        static::assertSame([0, null, 3], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinateNullElevation(): void
    {
        $point = new GeometryPoint(0, null, null, 4326);
        $coordinates = Coordinates::xyz(0, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZ);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, null], $point->toArray());
        static::assertSame([0, null, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinateElevation(): void
    {
        $point = new GeometryPoint(0, 2, 3, 4326);
        $coordinates = Coordinates::xyz(0, 2, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, 3], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZ);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, 3], $point->toArray());
        static::assertSame([0, 2, 3], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinateNullElevation(): void
    {
        $point = new GeometryPoint(0, 2, null, 4326);
        $coordinates = Coordinates::xyz(0, 2, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZ);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, null], $point->toArray());
        static::assertSame([0, 2, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinateElevation(): void
    {
        $point = new GeometryPoint(null, null, 3, 4326);
        $coordinates = Coordinates::xyz(null, null, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, 3], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZ);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, null, 3], $point->toArray());
        static::assertSame([null, null, 3], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinateNullElevation(): void
    {
        $point = new GeometryPoint(null, null, null, 4326);
        $coordinates = Coordinates::xyz(null, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZ);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertTrue($point->isEmpty());
        static::assertSame([], $point->toArray());
        static::assertSame([null, null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinateElevation(): void
    {
        $point = new GeometryPoint(null, 2, 3, 4326);
        $coordinates = Coordinates::xyz(null, 2, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, 3], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZ);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, 3], $point->toArray());
        static::assertSame([null, 2, 3], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinateNullElevation(): void
    {
        $point = new GeometryPoint(null, 2, null, 4326);
        $coordinates = Coordinates::xyz(null, 2, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZ);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, null], $point->toArray());
        static::assertSame([null, 2, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertSame(CoordinateDimensionEnum::XYZ, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
        static::assertFalse($original->isEmpty());
    }
}
