<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory\PublicFactory;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\CircularStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\CompoundCurveFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\GeographyCollectionFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\LineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\MultiLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\MultiPointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\MultiPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\PointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\PolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\PolyhedralSurfaceFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\TriangleFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\GeographyCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurfaceImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\TriangleImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Point;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\PolyhedralSurface;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\SpatialCollection;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Triangle;
use PHPUnit\Framework\TestCase;

/** Explicit factory coverage for XYZM Geography.
 * @internal
 *
 * @coversNothing
 */
final class Dimension4zmGeographyFactoryTest extends TestCase
{
    /** Restore the shared mode for subsequent tests. */
    protected function tearDown(): void
    {
        SpatialFactory::immutable();
        parent::tearDown();
    }

    /** Circular String Creates Both Representations. */
    public function testCircularStringCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = CircularStringFactory::create(points: [[0, null, 12, 7]], srid: $reference);
        $emptyImmutable = CircularStringFactory::create(points: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = CircularStringFactory::create(points: [[0, null, 12, 7]], srid: $reference);
        $emptyMutable = CircularStringFactory::create(points: [], srid: $reference);

        static::assertInstanceOf(CircularStringImmutable::class, $immutable);
        static::assertInstanceOf(CircularString::class, $mutable);
        static::assertSame([[0, null, 12, 7]], $immutable->toArray());
        static::assertSame([[0, null, 12, 7]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::CIRCULARSTRING, $immutable->getType());
        static::assertSame(GeometryTypeEnum::CIRCULARSTRING, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Compound Curve Creates Both Representations. */
    public function testCompoundCurveCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = CompoundCurveFactory::create(curves: [LineStringFactory::create(points: [[0, null, 12, 7]], srid: $reference)], srid: $reference);
        $emptyImmutable = CompoundCurveFactory::create(curves: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = CompoundCurveFactory::create(curves: [LineStringFactory::create(points: [[0, null, 12, 7]], srid: $reference)], srid: $reference);
        $emptyMutable = CompoundCurveFactory::create(curves: [], srid: $reference);

        static::assertInstanceOf(CompoundCurveImmutable::class, $immutable);
        static::assertInstanceOf(CompoundCurve::class, $mutable);
        static::assertSame([[[0, null, 12, 7]]], $immutable->toArray());
        static::assertSame([[[0, null, 12, 7]]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $immutable->getType());
        static::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Geography Collection Creates Both Representations. */
    public function testGeographyCollectionCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = GeographyCollectionFactory::create(elements: [PointFactory::create(x: 0, y: null, z: 12, m: 7, srid: $reference)], srid: $reference);
        $emptyImmutable = GeographyCollectionFactory::create(elements: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = GeographyCollectionFactory::create(elements: [PointFactory::create(x: 0, y: null, z: 12, m: 7, srid: $reference)], srid: $reference);
        $emptyMutable = GeographyCollectionFactory::create(elements: [], srid: $reference);

        static::assertInstanceOf(GeographyCollectionImmutable::class, $immutable);
        static::assertInstanceOf(SpatialCollection::class, $mutable);
        static::assertSame([[0, null, 12, 7]], $immutable->toArray());
        static::assertSame([[0, null, 12, 7]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $immutable->getType());
        static::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Line String Creates Both Representations. */
    public function testLineStringCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = LineStringFactory::create(points: [[0, null, 12, 7]], srid: $reference);
        $emptyImmutable = LineStringFactory::create(points: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = LineStringFactory::create(points: [[0, null, 12, 7]], srid: $reference);
        $emptyMutable = LineStringFactory::create(points: [], srid: $reference);

        static::assertInstanceOf(LineStringImmutable::class, $immutable);
        static::assertInstanceOf(LineString::class, $mutable);
        static::assertSame([[0, null, 12, 7]], $immutable->toArray());
        static::assertSame([[0, null, 12, 7]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::LINESTRING, $immutable->getType());
        static::assertSame(GeometryTypeEnum::LINESTRING, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Multi Line String Creates Both Representations. */
    public function testMultiLineStringCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = MultiLineStringFactory::create(lineStrings: [[[0, null, 12, 7]]], srid: $reference);
        $emptyImmutable = MultiLineStringFactory::create(lineStrings: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = MultiLineStringFactory::create(lineStrings: [[[0, null, 12, 7]]], srid: $reference);
        $emptyMutable = MultiLineStringFactory::create(lineStrings: [], srid: $reference);

        static::assertInstanceOf(MultiLineStringImmutable::class, $immutable);
        static::assertInstanceOf(MultiLineString::class, $mutable);
        static::assertSame([[[0, null, 12, 7]]], $immutable->toArray());
        static::assertSame([[[0, null, 12, 7]]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::MULTILINESTRING, $immutable->getType());
        static::assertSame(GeometryTypeEnum::MULTILINESTRING, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Multi Point Creates Both Representations. */
    public function testMultiPointCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = MultiPointFactory::create(points: [[0, null, 12, 7]], srid: $reference);
        $emptyImmutable = MultiPointFactory::create(points: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = MultiPointFactory::create(points: [[0, null, 12, 7]], srid: $reference);
        $emptyMutable = MultiPointFactory::create(points: [], srid: $reference);

        static::assertInstanceOf(MultiPointImmutable::class, $immutable);
        static::assertInstanceOf(MultiPoint::class, $mutable);
        static::assertSame([[0, null, 12, 7]], $immutable->toArray());
        static::assertSame([[0, null, 12, 7]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::MULTIPOINT, $immutable->getType());
        static::assertSame(GeometryTypeEnum::MULTIPOINT, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Multi Polygon Creates Both Representations. */
    public function testMultiPolygonCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = MultiPolygonFactory::create(polygons: [[[[0, null, 12, 7]]]], srid: $reference);
        $emptyImmutable = MultiPolygonFactory::create(polygons: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = MultiPolygonFactory::create(polygons: [[[[0, null, 12, 7]]]], srid: $reference);
        $emptyMutable = MultiPolygonFactory::create(polygons: [], srid: $reference);

        static::assertInstanceOf(MultiPolygonImmutable::class, $immutable);
        static::assertInstanceOf(MultiPolygon::class, $mutable);
        static::assertSame([[[[0, null, 12, 7]]]], $immutable->toArray());
        static::assertSame([[[[0, null, 12, 7]]]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::MULTIPOLYGON, $immutable->getType());
        static::assertSame(GeometryTypeEnum::MULTIPOLYGON, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Point Creates Both Representations. */
    public function testPointCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = PointFactory::create(x: 0, y: null, z: 12, m: 7, srid: $reference);
        $emptyImmutable = PointFactory::create(srid: $reference);
        SpatialFactory::mutable();
        $mutable = PointFactory::create(x: 0, y: null, z: 12, m: 7, srid: $reference);
        $emptyMutable = PointFactory::create(srid: $reference);

        static::assertInstanceOf(PointImmutable::class, $immutable);
        static::assertInstanceOf(Point::class, $mutable);
        static::assertSame([0, null, 12, 7], $immutable->toArray());
        static::assertSame([0, null, 12, 7], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::POINT, $immutable->getType());
        static::assertSame(GeometryTypeEnum::POINT, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Polygon Creates Both Representations. */
    public function testPolygonCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = PolygonFactory::create(rings: [[[0, null, 12, 7]]], srid: $reference);
        $emptyImmutable = PolygonFactory::create(rings: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = PolygonFactory::create(rings: [[[0, null, 12, 7]]], srid: $reference);
        $emptyMutable = PolygonFactory::create(rings: [], srid: $reference);

        static::assertInstanceOf(PolygonImmutable::class, $immutable);
        static::assertInstanceOf(Polygon::class, $mutable);
        static::assertSame([[[0, null, 12, 7]]], $immutable->toArray());
        static::assertSame([[[0, null, 12, 7]]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::POLYGON, $immutable->getType());
        static::assertSame(GeometryTypeEnum::POLYGON, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Polyhedral Surface Creates Both Representations. */
    public function testPolyhedralSurfaceCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = PolyhedralSurfaceFactory::create(patches: [[[[0, null, 12, 7]]]], srid: $reference);
        $emptyImmutable = PolyhedralSurfaceFactory::create(patches: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = PolyhedralSurfaceFactory::create(patches: [[[[0, null, 12, 7]]]], srid: $reference);
        $emptyMutable = PolyhedralSurfaceFactory::create(patches: [], srid: $reference);

        static::assertInstanceOf(PolyhedralSurfaceImmutable::class, $immutable);
        static::assertInstanceOf(PolyhedralSurface::class, $mutable);
        static::assertSame([[[[0, null, 12, 7]]]], $immutable->toArray());
        static::assertSame([[[[0, null, 12, 7]]]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $immutable->getType());
        static::assertSame(GeometryTypeEnum::POLYHEDRALSURFACE, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Triangle Creates Both Representations. */
    public function testTriangleCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = TriangleFactory::create(rings: [[[0, null, 12, 7]]], srid: $reference);
        $emptyImmutable = TriangleFactory::create(rings: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = TriangleFactory::create(rings: [[[0, null, 12, 7]]], srid: $reference);
        $emptyMutable = TriangleFactory::create(rings: [], srid: $reference);

        static::assertInstanceOf(TriangleImmutable::class, $immutable);
        static::assertInstanceOf(Triangle::class, $mutable);
        static::assertSame([[[0, null, 12, 7]]], $immutable->toArray());
        static::assertSame([[[0, null, 12, 7]]], $mutable->toArray());
        static::assertSame([], $emptyImmutable->toArray());
        static::assertSame([], $emptyMutable->toArray());
        static::assertSame(CoordinateDimensionEnum::XYZM, $immutable->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYZM, $mutable->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        static::assertSame(GeometryTypeEnum::TRIANGLE, $immutable->getType());
        static::assertSame(GeometryTypeEnum::TRIANGLE, $mutable->getType());
        static::assertSame($reference, $immutable->getSpatialReference());
        static::assertSame($reference, $mutable->getSpatialReference());
        static::assertSame($reference, $emptyImmutable->getSpatialReference());
        static::assertSame($reference, $emptyMutable->getSpatialReference());
    }
}
