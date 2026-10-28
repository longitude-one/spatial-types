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
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Point as GeographyPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Point as GeometryPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\Polygon;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for issue #36.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPoint
 */
class PartiallyNullXYZMPointTest extends TestCase
{
    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinateElevationMeasure(): void
    {
        $point = new GeographyPoint(0, null, 3, 4, 4326);
        $coordinates = Coordinates::xyzm(0, null, 3, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, 3, 4], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, 3, 4], $point->toArray());
        static::assertSame([0, null, 3, 4], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinateElevationNullMeasure(): void
    {
        $point = new GeographyPoint(0, null, 3, null, 4326);
        $coordinates = Coordinates::xyzm(0, null, 3, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, 3, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, 3, null], $point->toArray());
        static::assertSame([0, null, 3, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinateNullElevationMeasure(): void
    {
        $point = new GeographyPoint(0, null, null, 4, 4326);
        $coordinates = Coordinates::xyzm(0, null, null, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, null, 4], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, null, 4], $point->toArray());
        static::assertSame([0, null, null, 4], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinateNullElevationNullMeasure(): void
    {
        $point = new GeographyPoint(0, null, null, null, 4326);
        $coordinates = Coordinates::xyzm(0, null, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, null, null], $point->toArray());
        static::assertSame([0, null, null, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinateElevationMeasure(): void
    {
        $point = new GeographyPoint(0, 2, 3, 4, 4326);
        $coordinates = Coordinates::xyzm(0, 2, 3, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, 3, 4], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, 3, 4], $point->toArray());
        static::assertSame([0, 2, 3, 4], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinateElevationNullMeasure(): void
    {
        $point = new GeographyPoint(0, 2, 3, null, 4326);
        $coordinates = Coordinates::xyzm(0, 2, 3, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, 3, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, 3, null], $point->toArray());
        static::assertSame([0, 2, 3, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinateNullElevationMeasure(): void
    {
        $point = new GeographyPoint(0, 2, null, 4, 4326);
        $coordinates = Coordinates::xyzm(0, 2, null, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, null, 4], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, null, 4], $point->toArray());
        static::assertSame([0, 2, null, 4], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinateNullElevationNullMeasure(): void
    {
        $point = new GeographyPoint(0, 2, null, null, 4326);
        $coordinates = Coordinates::xyzm(0, 2, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, null, null], $point->toArray());
        static::assertSame([0, 2, null, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinateElevationMeasure(): void
    {
        $point = new GeographyPoint(null, null, 3, 4, 4326);
        $coordinates = Coordinates::xyzm(null, null, 3, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, 3, 4], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, null, 3, 4], $point->toArray());
        static::assertSame([null, null, 3, 4], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinateElevationNullMeasure(): void
    {
        $point = new GeographyPoint(null, null, 3, null, 4326);
        $coordinates = Coordinates::xyzm(null, null, 3, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, 3, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, null, 3, null], $point->toArray());
        static::assertSame([null, null, 3, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinateNullElevationMeasure(): void
    {
        $point = new GeographyPoint(null, null, null, 4, 4326);
        $coordinates = Coordinates::xyzm(null, null, null, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, null, 4], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, null, null, 4], $point->toArray());
        static::assertSame([null, null, null, 4], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinateNullElevationNullMeasure(): void
    {
        $point = new GeographyPoint(null, null, null, null, 4326);
        $coordinates = Coordinates::xyzm(null, null, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertTrue($point->isEmpty());
        static::assertSame([], $point->toArray());
        static::assertSame([null, null, null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinateElevationMeasure(): void
    {
        $point = new GeographyPoint(null, 2, 3, 4, 4326);
        $coordinates = Coordinates::xyzm(null, 2, 3, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, 3, 4], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, 3, 4], $point->toArray());
        static::assertSame([null, 2, 3, 4], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinateElevationNullMeasure(): void
    {
        $point = new GeographyPoint(null, 2, 3, null, 4326);
        $coordinates = Coordinates::xyzm(null, 2, 3, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, 3, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, 3, null], $point->toArray());
        static::assertSame([null, 2, 3, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinateNullElevationMeasure(): void
    {
        $point = new GeographyPoint(null, 2, null, 4, 4326);
        $coordinates = Coordinates::xyzm(null, 2, null, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, null, 4], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, null, 4], $point->toArray());
        static::assertSame([null, 2, null, 4], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinateNullElevationNullMeasure(): void
    {
        $point = new GeographyPoint(null, 2, null, null, 4326);
        $coordinates = Coordinates::xyzm(null, 2, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYZM);
        $original = new GeographyPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, null, null], $point->toArray());
        static::assertSame([null, 2, null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinateElevationMeasure(): void
    {
        $point = new GeometryPoint(0, null, 3, 4, 4326);
        $coordinates = Coordinates::xyzm(0, null, 3, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, 3, 4], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, 3, 4], $point->toArray());
        static::assertSame([0, null, 3, 4], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinateElevationNullMeasure(): void
    {
        $point = new GeometryPoint(0, null, 3, null, 4326);
        $coordinates = Coordinates::xyzm(0, null, 3, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, 3, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, 3, null], $point->toArray());
        static::assertSame([0, null, 3, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinateNullElevationMeasure(): void
    {
        $point = new GeometryPoint(0, null, null, 4, 4326);
        $coordinates = Coordinates::xyzm(0, null, null, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, null, 4], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, null, 4], $point->toArray());
        static::assertSame([0, null, null, 4], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinateNullElevationNullMeasure(): void
    {
        $point = new GeometryPoint(0, null, null, null, 4326);
        $coordinates = Coordinates::xyzm(0, null, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, null, null, null], $point->toArray());
        static::assertSame([0, null, null, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinateElevationMeasure(): void
    {
        $point = new GeometryPoint(0, 2, 3, 4, 4326);
        $coordinates = Coordinates::xyzm(0, 2, 3, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, 3, 4], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, 3, 4], $point->toArray());
        static::assertSame([0, 2, 3, 4], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinateElevationNullMeasure(): void
    {
        $point = new GeometryPoint(0, 2, 3, null, 4326);
        $coordinates = Coordinates::xyzm(0, 2, 3, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, 3, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, 3, null], $point->toArray());
        static::assertSame([0, 2, 3, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinateNullElevationMeasure(): void
    {
        $point = new GeometryPoint(0, 2, null, 4, 4326);
        $coordinates = Coordinates::xyzm(0, 2, null, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, null, 4], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, null, 4], $point->toArray());
        static::assertSame([0, 2, null, 4], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinateNullElevationNullMeasure(): void
    {
        $point = new GeometryPoint(0, 2, null, null, 4326);
        $coordinates = Coordinates::xyzm(0, 2, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([0, 2, null, null], $point->toArray());
        static::assertSame([0, 2, null, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinateElevationMeasure(): void
    {
        $point = new GeometryPoint(null, null, 3, 4, 4326);
        $coordinates = Coordinates::xyzm(null, null, 3, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, 3, 4], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, null, 3, 4], $point->toArray());
        static::assertSame([null, null, 3, 4], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinateElevationNullMeasure(): void
    {
        $point = new GeometryPoint(null, null, 3, null, 4326);
        $coordinates = Coordinates::xyzm(null, null, 3, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, 3, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, null, 3, null], $point->toArray());
        static::assertSame([null, null, 3, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinateNullElevationMeasure(): void
    {
        $point = new GeometryPoint(null, null, null, 4, 4326);
        $coordinates = Coordinates::xyzm(null, null, null, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, null, 4], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, null, null, 4], $point->toArray());
        static::assertSame([null, null, null, 4], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinateNullElevationNullMeasure(): void
    {
        $point = new GeometryPoint(null, null, null, null, 4326);
        $coordinates = Coordinates::xyzm(null, null, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertTrue($point->isEmpty());
        static::assertSame([], $point->toArray());
        static::assertSame([null, null, null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinateElevationMeasure(): void
    {
        $point = new GeometryPoint(null, 2, 3, 4, 4326);
        $coordinates = Coordinates::xyzm(null, 2, 3, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, 3, 4], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, 3, 4], $point->toArray());
        static::assertSame([null, 2, 3, 4], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinateElevationNullMeasure(): void
    {
        $point = new GeometryPoint(null, 2, 3, null, 4326);
        $coordinates = Coordinates::xyzm(null, 2, 3, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, 3, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, 3, null], $point->toArray());
        static::assertSame([null, 2, 3, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinateNullElevationMeasure(): void
    {
        $point = new GeometryPoint(null, 2, null, 4, 4326);
        $coordinates = Coordinates::xyzm(null, 2, null, 4);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, null, 4], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, null, 4], $point->toArray());
        static::assertSame([null, 2, null, 4], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertSame(4, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinateNullElevationNullMeasure(): void
    {
        $point = new GeometryPoint(null, 2, null, null, 4326);
        $coordinates = Coordinates::xyzm(null, 2, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYZM);
        $original = new GeometryPoint(1, 2, 3, 4, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertFalse($point->isEmpty());
        static::assertSame([null, 2, null, null], $point->toArray());
        static::assertSame([null, 2, null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getZ());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3, 4], $original->toArray());
        static::assertFalse($original->isEmpty());
    }

    /** Shared polygon hydration and replacement preserve partially supplied points. */
    public function testGeometryPolygonPathsPreserveNullOrdinates(): void
    {
        $polygon = new Polygon([[[0, null, 3, null], [null, 2, null, 4], [0, null, 3, null]]], 3857);
        $replacement = $polygon->withPoint(0, 1, Coordinates::xyzm(null, null, 5, null));

        static::assertSame([[[0, null, 3, null], [null, 2, null, 4], [0, null, 3, null]]], $polygon->toArray());
        static::assertSame([[[0, null, 3, null], [null, null, 5, null], [0, null, 3, null]]], $replacement->toArray());
        static::assertSame(3857, $replacement->getSrid());
    }
}
