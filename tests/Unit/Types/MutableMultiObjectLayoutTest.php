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
        $collection = new SpatialCollection([$point, $line, $arc, $compound, $polygon, $triangle, $surface, $points, $lines, $polygons, $nested]);
        static::assertSame([$point, $line, $arc, $compound, $polygon, $triangle, $surface, $points, $lines, $polygons, $nested], $collection->getElements());
        static::assertSame($collection, $collection->setFamily(SpatialModelEnum::GEOGRAPHY)->setSrid(2154));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $arc->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $triangle->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $surface->getFamily());
        static::assertSame(2154, $surface->getPatch(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame(2154, $arc->getPoint(0)->getSrid());
        static::assertSame(2154, $nested->getSrid());
        static::assertSame([0, null], $point->toArray());
    }

    /** Empty members and invalid representable geometry are kept. */
    public function testEmptyMembers(): void
    {
        $points = new MultiPoint([[null, null], [0, null]]);
        $lines = new MultiLineString([[], [[0, null]]]);
        $polygons = new MultiPolygon([[], [[]], [[[0, null]]]]);
        $collection = new SpatialCollection([$points, $lines, $polygons]);
        static::assertFalse($points->isEmpty());
        static::assertSame([[], [0, null]], $points->toArray());
        static::assertSame([[], [[0, null]]], $lines->toArray());
        static::assertSame([[], [[]], [[[0, null]]]], $polygons->toArray());
        static::assertFalse($collection->isEmpty());
        static::assertSame([[[], [0, null]], [[], [[0, null]]], [[], [[]], [[[0, null]]]]], $collection->toArray());
    }

    /** Mutable aggregates expose their mutation contracts without copy or layout mutation methods. */
    public function testPublicContracts(): void
    {
        $points = new MultiPoint();
        $lines = new MultiLineString();
        $polygons = new MultiPolygon();
        $collection = new SpatialCollection();
        static::assertContains(MutableMultiPointInterface::class, class_implements($points));
        static::assertContains(MutableMultiLineStringInterface::class, class_implements($lines));
        static::assertContains(MutableMultiPolygonInterface::class, class_implements($polygons));
        static::assertContains(MutableSpatialInterface::class, class_implements($collection));
        static::assertSame(SpatialModelEnum::GEOMETRY, $points->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $lines->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $polygons->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $collection->getFamily());
        static::assertFalse((new \ReflectionClass($points))->hasMethod('withPoint'));
        static::assertFalse((new \ReflectionClass($lines))->hasMethod('withLineString'));
        static::assertFalse((new \ReflectionClass($polygons))->hasMethod('withPolygon'));
        static::assertFalse((new \ReflectionClass($collection))->hasMethod('withElement'));
        static::assertFalse((new \ReflectionClass($collection))->hasMethod('withSrid'));
        static::assertFalse((new \ReflectionClass($points))->hasMethod('setDimension'));
        static::assertSame('static', (string) (new \ReflectionMethod($collection, 'setElements'))->getReturnType());
    }

    /** Each collection kind hydrates mutable descendants in the declared layout. */
    public function testXyClasses(): void
    {
        $points = new MultiPoint([[0, null]], 4326, SpatialModelEnum::GEOGRAPHY);
        $lines = new MultiLineString([[[0, null]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygons = new MultiPolygon([[[[0, null]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $collection = new SpatialCollection([$points, $lines, $polygons], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null]], $points->toArray());
        static::assertSame([[[0, null]]], $lines->toArray());
        static::assertSame([[[[0, null]]]], $polygons->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $points->getDimension());
        static::assertSame(CoordinateDimensionEnum::XY, $lines->getDimension());
        static::assertSame(CoordinateDimensionEnum::XY, $polygons->getDimension());
        static::assertSame(CoordinateDimensionEnum::XY, $collection->getDimension());
        static::assertInstanceOf(Point::class, $points->getElement(0));
        static::assertInstanceOf(LineString::class, $lines->getElement(0));
        static::assertInstanceOf(Polygon::class, $polygons->getElement(0));
        static::assertInstanceOf(Point::class, $polygons->getElement(0)->getRing(0)->getPoint(0));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
        static::assertSame(4326, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame([$points, $lines, $polygons], $collection->getElements());
        static::assertSame(GeometryTypeEnum::MULTIPOINT, $points->getType());
        static::assertSame(GeometryTypeEnum::MULTILINESTRING, $lines->getType());
        static::assertSame(GeometryTypeEnum::MULTIPOLYGON, $polygons->getType());
        static::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $collection->getType());
    }

    /** Each collection kind hydrates mutable descendants in the declared layout. */
    public function testXymClasses(): void
    {
        $points = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPoint([[0, null, 7]], 4326, SpatialModelEnum::GEOGRAPHY);
        $lines = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiLineString([[[0, null, 7]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygons = new \LongitudeOne\SpatialTypes\Types\Dimension3m\MultiPolygon([[[[0, null, 7]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension3m\SpatialCollection([$points, $lines, $polygons], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 7]], $points->toArray());
        static::assertSame([[[0, null, 7]]], $lines->toArray());
        static::assertSame([[[[0, null, 7]]]], $polygons->toArray());
        static::assertSame(CoordinateDimensionEnum::XYM, $points->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $lines->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $polygons->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $collection->getDimension());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Point::class, $points->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\LineString::class, $lines->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Polygon::class, $polygons->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3m\Point::class, $polygons->getElement(0)->getRing(0)->getPoint(0));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
        static::assertSame(4326, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame([$points, $lines, $polygons], $collection->getElements());
        static::assertSame(GeometryTypeEnum::MULTIPOINT, $points->getType());
        static::assertSame(GeometryTypeEnum::MULTILINESTRING, $lines->getType());
        static::assertSame(GeometryTypeEnum::MULTIPOLYGON, $polygons->getType());
        static::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $collection->getType());
    }

    /** Each collection kind hydrates mutable descendants in the declared layout. */
    public function testXyzClasses(): void
    {
        $points = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPoint([[0, null, 5]], 4326, SpatialModelEnum::GEOGRAPHY);
        $lines = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiLineString([[[0, null, 5]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygons = new \LongitudeOne\SpatialTypes\Types\Dimension3z\MultiPolygon([[[[0, null, 5]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension3z\SpatialCollection([$points, $lines, $polygons], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 5]], $points->toArray());
        static::assertSame([[[0, null, 5]]], $lines->toArray());
        static::assertSame([[[[0, null, 5]]]], $polygons->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZ, $points->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZ, $lines->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZ, $polygons->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZ, $collection->getDimension());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $points->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\LineString::class, $lines->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Polygon::class, $polygons->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension3z\Point::class, $polygons->getElement(0)->getRing(0)->getPoint(0));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
        static::assertSame(4326, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame([$points, $lines, $polygons], $collection->getElements());
        static::assertSame(GeometryTypeEnum::MULTIPOINT, $points->getType());
        static::assertSame(GeometryTypeEnum::MULTILINESTRING, $lines->getType());
        static::assertSame(GeometryTypeEnum::MULTIPOLYGON, $polygons->getType());
        static::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $collection->getType());
    }

    /** Each collection kind hydrates mutable descendants in the declared layout. */
    public function testXyzmClasses(): void
    {
        $points = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint([[0, null, 5, 7]], 4326, SpatialModelEnum::GEOGRAPHY);
        $lines = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString([[[0, null, 5, 7]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $polygons = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon([[[[0, null, 5, 7]]]], 4326, SpatialModelEnum::GEOGRAPHY);
        $collection = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection([$points, $lines, $polygons], 4326, SpatialModelEnum::GEOGRAPHY);
        static::assertSame([[0, null, 5, 7]], $points->toArray());
        static::assertSame([[[0, null, 5, 7]]], $lines->toArray());
        static::assertSame([[[[0, null, 5, 7]]]], $polygons->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $points->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $lines->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $polygons->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $collection->getDimension());
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $points->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString::class, $lines->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon::class, $polygons->getElement(0));
        static::assertInstanceOf(\LongitudeOne\SpatialTypes\Types\Dimension4zm\Point::class, $polygons->getElement(0)->getRing(0)->getPoint(0));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygons->getElement(0)->getRing(0)->getPoint(0)->getFamily());
        static::assertSame(4326, $polygons->getElement(0)->getRing(0)->getPoint(0)->getSrid());
        static::assertSame([$points, $lines, $polygons], $collection->getElements());
        static::assertSame(GeometryTypeEnum::MULTIPOINT, $points->getType());
        static::assertSame(GeometryTypeEnum::MULTILINESTRING, $lines->getType());
        static::assertSame(GeometryTypeEnum::MULTIPOLYGON, $polygons->getType());
        static::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $collection->getType());
    }
}
