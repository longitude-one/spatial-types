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

        self::assertSame([0, null], $point->toArray());
        self::assertSame([0, null], $coordinates->toArray());
        self::assertSame(0, $point->getX());
        self::assertNull($point->getY());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertEquals($coordinates, $point->getCoordinates());
        self::assertEquals($point, $factoryPoint);
        self::assertEquals($point, $replacement);
        self::assertNotSame($original, $replacement);
        self::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyAbscissaOrdinate(): void
    {
        $point = new GeographyPoint(0, 2, 4326);
        $coordinates = Coordinates::xy(0, 2);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XY);
        $original = new GeographyPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        self::assertSame([0, 2], $point->toArray());
        self::assertSame([0, 2], $coordinates->toArray());
        self::assertSame(0, $point->getX());
        self::assertSame(2, $point->getY());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertEquals($coordinates, $point->getCoordinates());
        self::assertEquals($point, $factoryPoint);
        self::assertEquals($point, $replacement);
        self::assertNotSame($original, $replacement);
        self::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaNullOrdinate(): void
    {
        $point = new GeographyPoint(null, null, 4326);
        $coordinates = Coordinates::xy(null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XY);
        $original = new GeographyPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        self::assertSame([], $point->toArray());
        self::assertSame([null, null], $coordinates->toArray());
        self::assertNull($point->getX());
        self::assertNull($point->getY());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertNull($point->getCoordinates());
        self::assertEquals($point, $factoryPoint);
        self::assertEquals($point, $replacement);
        self::assertNotSame($original, $replacement);
        self::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeographyNullAbscissaOrdinate(): void
    {
        $point = new GeographyPoint(null, 2, 4326);
        $coordinates = Coordinates::xy(null, 2);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2], 4326, SpatialModelEnum::GEOGRAPHY, CoordinateDimensionEnum::XY);
        $original = new GeographyPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        self::assertSame([null, 2], $point->toArray());
        self::assertSame([null, 2], $coordinates->toArray());
        self::assertNull($point->getX());
        self::assertSame(2, $point->getY());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertEquals($coordinates, $point->getCoordinates());
        self::assertEquals($point, $factoryPoint);
        self::assertEquals($point, $replacement);
        self::assertNotSame($original, $replacement);
        self::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaNullOrdinate(): void
    {
        $point = new GeometryPoint(0, null, 4326);
        $coordinates = Coordinates::xy(0, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XY);
        $original = new GeometryPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        self::assertSame([0, null], $point->toArray());
        self::assertSame([0, null], $coordinates->toArray());
        self::assertSame(0, $point->getX());
        self::assertNull($point->getY());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertEquals($coordinates, $point->getCoordinates());
        self::assertEquals($point, $factoryPoint);
        self::assertEquals($point, $replacement);
        self::assertNotSame($original, $replacement);
        self::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryAbscissaOrdinate(): void
    {
        $point = new GeometryPoint(0, 2, 4326);
        $coordinates = Coordinates::xy(0, 2);
        $factoryPoint = FromIndexedArrayFactory::createPoint([0, 2], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XY);
        $original = new GeometryPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        self::assertSame([0, 2], $point->toArray());
        self::assertSame([0, 2], $coordinates->toArray());
        self::assertSame(0, $point->getX());
        self::assertSame(2, $point->getY());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertEquals($coordinates, $point->getCoordinates());
        self::assertEquals($point, $factoryPoint);
        self::assertEquals($point, $replacement);
        self::assertNotSame($original, $replacement);
        self::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaNullOrdinate(): void
    {
        $point = new GeometryPoint(null, null, 4326);
        $coordinates = Coordinates::xy(null, null);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, null], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XY);
        $original = new GeometryPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        self::assertSame([], $point->toArray());
        self::assertSame([null, null], $coordinates->toArray());
        self::assertNull($point->getX());
        self::assertNull($point->getY());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertNull($point->getCoordinates());
        self::assertEquals($point, $factoryPoint);
        self::assertEquals($point, $replacement);
        self::assertNotSame($original, $replacement);
        self::assertSame([1, 2], $original->toArray());
    }

    /** Preserve supplied and null ordinates through every public point path. */
    public function testGeometryNullAbscissaOrdinate(): void
    {
        $point = new GeometryPoint(null, 2, 4326);
        $coordinates = Coordinates::xy(null, 2);
        $factoryPoint = FromIndexedArrayFactory::createPoint([null, 2], 4326, SpatialModelEnum::GEOMETRY, CoordinateDimensionEnum::XY);
        $original = new GeometryPoint(1, 2, 4326);
        $replacement = $original->withCoordinates($coordinates);

        self::assertSame([null, 2], $point->toArray());
        self::assertSame([null, 2], $coordinates->toArray());
        self::assertNull($point->getX());
        self::assertSame(2, $point->getY());
        self::assertSame(CoordinateDimensionEnum::XY, $point->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertSame(4326, $point->getSrid());
        self::assertEquals($coordinates, $point->getCoordinates());
        self::assertEquals($point, $factoryPoint);
        self::assertEquals($point, $replacement);
        self::assertNotSame($original, $replacement);
        self::assertSame([1, 2], $original->toArray());
    }
}
