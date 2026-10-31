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
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable as GeographyPoint;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable as GeometryPoint;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for issue #36.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPointImmutable
 */
class PartiallyNullXYMPointTest extends TestCase
{
    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinateMeasure(): void
    {
        $point = new GeographyPoint(0, null, 3, 4326);
        $coordinates = Coordinates::xym(0, null, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, 3], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYM);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, null, 3], $point->toArray());
        static::assertSame([0, null, 3], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaNullOrdinateNullMeasure(): void
    {
        $point = new GeographyPoint(0, null, null, 4326);
        $coordinates = Coordinates::xym(0, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYM);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, null, null], $point->toArray());
        static::assertSame([0, null, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinateMeasure(): void
    {
        $point = new GeographyPoint(0, 2, 3, 4326);
        $coordinates = Coordinates::xym(0, 2, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, 3], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYM);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, 2, 3], $point->toArray());
        static::assertSame([0, 2, 3], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinateNullMeasure(): void
    {
        $point = new GeographyPoint(0, 2, null, 4326);
        $coordinates = Coordinates::xym(0, 2, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYM);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, 2, null], $point->toArray());
        static::assertSame([0, 2, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Shared aggregate constructors and replacements preserve null ordinates. */
    public function testGeographyAggregatePathsPreserveNullOrdinates(): void
    {
        $line = new LineStringImmutable([[0, null, 3], [null, 2, null]], 4326);
        $replacement = $line->withPoint(1, Coordinates::xym(null, null, 4));
        $multiPoint = new MultiPointImmutable([[0, null, 3], [null, 2, null]], 4326);

        static::assertSame([[0, null, 3], [null, 2, null]], $line->toArray());
        static::assertSame([[0, null, 3], [null, null, 4]], $replacement->toArray());
        static::assertSame([[0, null, 3], [null, 2, null]], $multiPoint->toArray());
        static::assertSame(4326, $replacement->getPoint(1)->getSrid());
        static::assertSame(CoordinateDimensionEnum::XYM, $replacement->getPoint(1)->getDimension());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinateMeasure(): void
    {
        $point = new GeographyPoint(null, null, 3, 4326);
        $coordinates = Coordinates::xym(null, null, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, 3], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYM);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([null, null, 3], $point->toArray());
        static::assertSame([null, null, 3], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinateNullMeasure(): void
    {
        $point = new GeographyPoint(null, null, null, 4326);
        $coordinates = Coordinates::xym(null, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYM);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([], $point->toArray());
        static::assertSame([null, null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinateMeasure(): void
    {
        $point = new GeographyPoint(null, 2, 3, 4326);
        $coordinates = Coordinates::xym(null, 2, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, 3], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYM);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([null, 2, 3], $point->toArray());
        static::assertSame([null, 2, 3], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinateNullMeasure(): void
    {
        $point = new GeographyPoint(null, 2, null, 4326);
        $coordinates = Coordinates::xym(null, 2, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XYM);
        $original = new GeographyPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([null, 2, null], $point->toArray());
        static::assertSame([null, 2, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinateMeasure(): void
    {
        $point = new GeometryPoint(0, null, 3, 4326);
        $coordinates = Coordinates::xym(0, null, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, 3], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, null, 3], $point->toArray());
        static::assertSame([0, null, 3], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinateNullMeasure(): void
    {
        $point = new GeometryPoint(0, null, null, 4326);
        $coordinates = Coordinates::xym(0, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, null, null], $point->toArray());
        static::assertSame([0, null, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinateMeasure(): void
    {
        $point = new GeometryPoint(0, 2, 3, 4326);
        $coordinates = Coordinates::xym(0, 2, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, 3], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, 2, 3], $point->toArray());
        static::assertSame([0, 2, 3], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinateNullMeasure(): void
    {
        $point = new GeometryPoint(0, 2, null, 4326);
        $coordinates = Coordinates::xym(0, 2, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([0, 2, null], $point->toArray());
        static::assertSame([0, 2, null], $coordinates->toArray());
        static::assertSame(0, $point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinateMeasure(): void
    {
        $point = new GeometryPoint(null, null, 3, 4326);
        $coordinates = Coordinates::xym(null, null, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, 3], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([null, null, 3], $point->toArray());
        static::assertSame([null, null, 3], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinateNullMeasure(): void
    {
        $point = new GeometryPoint(null, null, null, 4326);
        $coordinates = Coordinates::xym(null, null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([], $point->toArray());
        static::assertSame([null, null, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertNull($point->getY());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertNull($point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinateMeasure(): void
    {
        $point = new GeometryPoint(null, 2, 3, 4326);
        $coordinates = Coordinates::xym(null, 2, 3);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, 3], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([null, 2, 3], $point->toArray());
        static::assertSame([null, 2, 3], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertSame(3, $point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinateNullMeasure(): void
    {
        $point = new GeometryPoint(null, 2, null, 4326);
        $coordinates = Coordinates::xym(null, 2, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XYM);
        $original = new GeometryPoint(1, 2, 3, 4326);
        $replacement = $original->withCoordinates($coordinates);

        static::assertSame([null, 2, null], $point->toArray());
        static::assertSame([null, 2, null], $coordinates->toArray());
        static::assertNull($point->getX());
        static::assertSame(2, $point->getY());
        static::assertNull($point->getM());
        static::assertSame(CoordinateDimensionEnum::XYM, $point->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(4326, $point->getSrid());
        static::assertEquals($coordinates, $point->getCoordinates());
        static::assertEquals($point, $factoryPoint);
        static::assertEquals($point, $replacement);
        static::assertNotSame($original, $replacement);
        static::assertSame([1, 2, 3], $original->toArray());
    }
}
