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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension3m\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Point;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Triangle;
use PHPUnit\Framework\TestCase;

/** Every mutable polygon and triangle layout hydrates mutable descendants.
 * @internal
 *
 * @coversNothing
 */
class MutablePolygonLayoutTest extends TestCase
{
    /** XYM Polygon retains ordinates, type and layout through replacement. */
    public function testXymPolygon(): void
    {
        $surface = new Polygon([[[0, null, 9]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYGON, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[0, null, 9]]], $surface->toArray());
        self::assertInstanceOf(LineString::class, $surface->getRing(0));
        self::assertInstanceOf(Point::class, $surface->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[null, 7, null]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[null, 7, null]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYGON, $surface->getType());
        self::assertSame(2154, $surface->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getRing(0)->getPoint(0)->getFamily());
    }

    /** XYM Triangle retains ordinates, type and layout through replacement. */
    public function testXymTriangle(): void
    {
        $surface = new Triangle([[[0, null, 9]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[0, null, 9]]], $surface->toArray());
        self::assertInstanceOf(LineString::class, $surface->getRing(0));
        self::assertInstanceOf(Point::class, $surface->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[null, 7, null]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[null, 7, null]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $surface->getType());
        self::assertSame(2154, $surface->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getRing(0)->getPoint(0)->getFamily());
    }

    /** XY Polygon retains ordinates, type and layout through replacement. */
    public function testXyPolygon(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension2\Polygon([[[0, null]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XY, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYGON, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[0, null]]], $surface->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension2\LineString::class, $surface->getRing(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension2\Point::class, $surface->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[null, 7]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[null, 7]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYGON, $surface->getType());
        self::assertSame(2154, $surface->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getRing(0)->getPoint(0)->getFamily());
    }

    /** XY Triangle retains ordinates, type and layout through replacement. */
    public function testXyTriangle(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension2\Triangle([[[0, null]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XY, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[0, null]]], $surface->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension2\LineString::class, $surface->getRing(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension2\Point::class, $surface->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[null, 7]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[null, 7]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $surface->getType());
        self::assertSame(2154, $surface->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getRing(0)->getPoint(0)->getFamily());
    }

    /** XYZM Polygon retains ordinates, type and layout through replacement. */
    public function testXyzmPolygon(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon([[[0, null, 8, 9]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYZM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYGON, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[0, null, 8, 9]]], $surface->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString::class, $surface->getRing(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $surface->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[null, 7, null, 0]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[null, 7, null, 0]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYGON, $surface->getType());
        self::assertSame(2154, $surface->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getRing(0)->getPoint(0)->getFamily());
    }

    /** XYZM Triangle retains ordinates, type and layout through replacement. */
    public function testXyzmTriangle(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Triangle([[[0, null, 8, 9]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYZM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[0, null, 8, 9]]], $surface->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString::class, $surface->getRing(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $surface->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[null, 7, null, 0]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[null, 7, null, 0]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $surface->getType());
        self::assertSame(2154, $surface->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getRing(0)->getPoint(0)->getFamily());
    }

    /** XYZ Polygon retains ordinates, type and layout through replacement. */
    public function testXyzPolygon(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([[[0, null, 8]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYZ, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYGON, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[0, null, 8]]], $surface->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\LineString::class, $surface->getRing(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $surface->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[null, 7, null]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[null, 7, null]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::POLYGON, $surface->getType());
        self::assertSame(2154, $surface->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getRing(0)->getPoint(0)->getFamily());
    }

    /** XYZ Triangle retains ordinates, type and layout through replacement. */
    public function testXyzTriangle(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Triangle([[[0, null, 8]]], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYZ, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $surface->getType());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame([[[0, null, 8]]], $surface->toArray());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\LineString::class, $surface->getRing(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $surface->getRing(0)->getPoint(0));
        self::assertSame($surface, $surface->setArrayOfCoordinates([[[null, 7, null]]])->setSrid(2154)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame([[[null, 7, null]]], $surface->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $surface->getDimension());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $surface->getType());
        self::assertSame(2154, $surface->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(SpatialModelEnum::GEOMETRY, $surface->getRing(0)->getPoint(0)->getFamily());
    }
}
