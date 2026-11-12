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
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableMultiPolygonInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Triangle;
use LongitudeOne\SpatialTypes\Types\Dimension3z\PolyhedralSurface;
use PHPUnit\Framework\TestCase;

/** Mutable aggregate contracts.
 * @internal
 *
 * @coversNothing
 */
class MutableMultiObjectLayoutTest extends TestCase
{
    /** SpatialCollection accepts every supported mutable spatial type and nested collections. */
    public function testAllMutableMemberKinds(): void
    {
        $point = new Point(0, null);
        $line = new LineString([$point]);
        $arc = new CircularString([[1, 2]]);
        $compound = new CompoundCurve([$line, $arc]);
        $polygon = new Polygon([$line]);
        $triangle = new Triangle([$line]);
        $surface = new PolyhedralSurface([new \LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon([[[1, 2, 3]]])]);
        $points = new MultiPoint([$point]);
        $lines = new MultiLineString([$line]);
        $polygons = new MultiPolygon([$polygon]);
        $nested = new SpatialCollection([]);
        $collection = new SpatialCollection([$point, $line, $arc, $compound, $polygon, $triangle, $points, $lines, $polygons, $nested]);
        self::assertSame([$point, $line, $arc, $compound, $polygon, $triangle, $points, $lines, $polygons, $nested], $collection->getElements());
        self::assertSame($collection, $collection->setFamily(SpatialModelEnum::GEOGRAPHY)->setSrid(2154));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $arc->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $triangle->getFamily());
        $surfaceCollection = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$surface]);
        $surfaceCollection->setFamily(SpatialModelEnum::GEOGRAPHY)->setSrid(2154);
        self::assertSame($surface, $surfaceCollection->getElement(0));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        self::assertSame(2154, $surface->getPatch(0)->getRing(0)->getPoint(0)->getSrid());
        self::assertSame(2154, $arc->getPoint(0)->getSrid());
        self::assertSame(2154, $nested->getSrid());
        self::assertSame([0, null], $point->toArray());
    }

    /** Empty members and invalid representable geometry are kept. */
    public function testEmptyMembers(): void
    {
        $points = new MultiPoint([[null, null], [0, null]]);
        $lines = new MultiLineString([[], [[0, null]]]);
        $polygons = new MultiPolygon([[], [[]], [[[0, null]]]]);
        $collection = new SpatialCollection([$points, $lines, $polygons]);
        self::assertSame([[], [0, null]], $points->toArray());
        self::assertSame([[], [[0, null]]], $lines->toArray());
        self::assertSame([[], [[]], [[[0, null]]]], $polygons->toArray());
        self::assertSame([[[], [0, null]], [[], [[0, null]]], [[], [[]], [[[0, null]]]]], $collection->toArray());
    }

    /** Mutable aggregates expose their mutation contracts without copy or layout mutation methods. */
    public function testPublicContracts(): void
    {
        $points = new MultiPoint();
        $lines = new MultiLineString();
        $polygons = new MultiPolygon();
        $collection = new SpatialCollection();
        self::assertContains(MutableMultiPointInterface::class, class_implements($points));
        self::assertContains(MutableMultiLineStringInterface::class, class_implements($lines));
        self::assertContains(MutableMultiPolygonInterface::class, class_implements($polygons));
        self::assertContains(MutableSpatialInterface::class, class_implements($collection));
        self::assertSame(SpatialModelEnum::GEOMETRY, $points->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $lines->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $polygons->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $collection->getFamily());
        self::assertFalse(new \ReflectionClass($points)->hasMethod('withPoint'));
        self::assertFalse(new \ReflectionClass($lines)->hasMethod('withLineString'));
        self::assertFalse(new \ReflectionClass($polygons)->hasMethod('withPolygon'));
        self::assertFalse(new \ReflectionClass($collection)->hasMethod('withElement'));
        self::assertFalse(new \ReflectionClass($collection)->hasMethod('withSrid'));
        self::assertFalse(new \ReflectionClass($points)->hasMethod('setDimension'));
        self::assertSame('static', (string) new \ReflectionMethod($collection, 'setElements')->getReturnType());
    }

    /** Each collection kind hydrates mutable descendants in the declared layout. */
    public function testXyClasses(): void
    {
        $points = new MultiPoint([[0, null]], 4326, SpatialModelEnum::GEOGRAPHY);
        $lines = new MultiLineString([[[0, null]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygons = new MultiPolygon([[[[0, null]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $collection = new SpatialCollection([$points, $lines, $polygons], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null]], $points->toArray());
        self::assertSame([[[0, null]]], $lines->toArray());
        self::assertSame([[[[0, null]]]], $polygons->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $points->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $lines->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $polygons->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $collection->getDimension());
        self::assertInstanceOf(Point::class, $points->getElement(0));
        self::assertInstanceOf(LineString::class, $lines->getElement(0));
        self::assertInstanceOf(Polygon::class, $polygons->getElement(0));
        self::assertInstanceOf(Point::class, $polygons->getElement(0)->getRing(0)->getPoint(0));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
        self::assertSame(4326, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        self::assertSame([$points, $lines, $polygons], $collection->getElements());
        self::assertSame(GeometryTypeEnum::MULTIPOINT, $points->getType());
        self::assertSame(GeometryTypeEnum::MULTILINESTRING, $lines->getType());
        self::assertSame(GeometryTypeEnum::MULTIPOLYGON, $polygons->getType());
        self::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $collection->getType());
    }

    /** Each collection kind hydrates mutable descendants in the declared layout. */
    public function testXymClasses(): void
    {
        $points = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([[0, null, 7]], 4326, SpatialModelEnum::GEOGRAPHY);
        $lines = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiLineString([[[0, null, 7]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygons = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon([[[[0, null, 7]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$points, $lines, $polygons], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 7]], $points->toArray());
        self::assertSame([[[0, null, 7]]], $lines->toArray());
        self::assertSame([[[[0, null, 7]]]], $polygons->toArray());
        self::assertSame(CoordinateDimensionEnum::XYM, $points->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYM, $lines->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYM, $polygons->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYM, $collection->getDimension());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Point::class, $points->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\LineString::class, $lines->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon::class, $polygons->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Point::class, $polygons->getElement(0)->getRing(0)->getPoint(0));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
        self::assertSame(4326, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        self::assertSame([$points, $lines, $polygons], $collection->getElements());
        self::assertSame(GeometryTypeEnum::MULTIPOINT, $points->getType());
        self::assertSame(GeometryTypeEnum::MULTILINESTRING, $lines->getType());
        self::assertSame(GeometryTypeEnum::MULTIPOLYGON, $polygons->getType());
        self::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $collection->getType());
    }

    /** Each collection kind hydrates mutable descendants in the declared layout. */
    public function testXyzClasses(): void
    {
        $points = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([[0, null, 5]], 4326, SpatialModelEnum::GEOGRAPHY);
        $lines = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiLineString([[[0, null, 5]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygons = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPolygon([[[[0, null, 5]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$points, $lines, $polygons], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 5]], $points->toArray());
        self::assertSame([[[0, null, 5]]], $lines->toArray());
        self::assertSame([[[[0, null, 5]]]], $polygons->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZ, $points->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZ, $lines->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZ, $polygons->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZ, $collection->getDimension());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $points->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\LineString::class, $lines->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon::class, $polygons->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $polygons->getElement(0)->getRing(0)->getPoint(0));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
        self::assertSame(4326, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        self::assertSame([$points, $lines, $polygons], $collection->getElements());
        self::assertSame(GeometryTypeEnum::MULTIPOINT, $points->getType());
        self::assertSame(GeometryTypeEnum::MULTILINESTRING, $lines->getType());
        self::assertSame(GeometryTypeEnum::MULTIPOLYGON, $polygons->getType());
        self::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $collection->getType());
    }

    /** Each collection kind hydrates mutable descendants in the declared layout. */
    public function testXyzmClasses(): void
    {
        $points = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([[0, null, 5, 7]], 4326, SpatialModelEnum::GEOGRAPHY);
        $lines = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString([[[0, null, 5, 7]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygons = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([[[[0, null, 5, 7]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$points, $lines, $polygons], 4326, SpatialModelEnum::GEOGRAPHY);
        self::assertSame([[0, null, 5, 7]], $points->toArray());
        self::assertSame([[[0, null, 5, 7]]], $lines->toArray());
        self::assertSame([[[[0, null, 5, 7]]]], $polygons->toArray());
        self::assertSame(CoordinateDimensionEnum::XYZM, $points->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZM, $lines->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZM, $polygons->getDimension());
        self::assertSame(CoordinateDimensionEnum::XYZM, $collection->getDimension());
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $points->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString::class, $lines->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon::class, $polygons->getElement(0));
        self::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $polygons->getElement(0)->getRing(0)->getPoint(0));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
        self::assertSame(4326, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        self::assertSame([$points, $lines, $polygons], $collection->getElements());
        self::assertSame(GeometryTypeEnum::MULTIPOINT, $points->getType());
        self::assertSame(GeometryTypeEnum::MULTILINESTRING, $lines->getType());
        self::assertSame(GeometryTypeEnum::MULTIPOLYGON, $polygons->getType());
        self::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $collection->getType());
    }
}
