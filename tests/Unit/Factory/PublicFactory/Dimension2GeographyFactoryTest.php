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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Factory\PublicFactory;

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\CircularStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\CompoundCurveFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\GeographyCollectionFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\LineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\MultiLineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\MultiPointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\MultiPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\PointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\PolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geography\TriangleFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\GeographyCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geography\TriangleImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension2\SpatialCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Triangle;
use PHPUnit\Framework\TestCase;

/** Explicit factory coverage for XY Geography.
 * @internal
 *
 * @coversNothing
 */
final class Dimension2GeographyFactoryTest extends TestCase
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
        $immutable = CircularStringFactory::create(points: [[0, null]], srid: $reference);
        $emptyImmutable = CircularStringFactory::create(points: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = CircularStringFactory::create(points: [[0, null]], srid: $reference);
        $emptyMutable = CircularStringFactory::create(points: [], srid: $reference);

        self::assertInstanceOf(CircularStringImmutable::class, $immutable);
        self::assertInstanceOf(CircularString::class, $mutable);
        self::assertSame([[0, null]], $immutable->toArray());
        self::assertSame([[0, null]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::CIRCULARSTRING, $immutable->getType());
        self::assertSame(GeometryTypeEnum::CIRCULARSTRING, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Compound Curve Creates Both Representations. */
    public function testCompoundCurveCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = CompoundCurveFactory::create(curves: [LineStringFactory::create(points: [[0, null]], srid: $reference)], srid: $reference);
        $emptyImmutable = CompoundCurveFactory::create(curves: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = CompoundCurveFactory::create(curves: [LineStringFactory::create(points: [[0, null]], srid: $reference)], srid: $reference);
        $emptyMutable = CompoundCurveFactory::create(curves: [], srid: $reference);

        self::assertInstanceOf(CompoundCurveImmutable::class, $immutable);
        self::assertInstanceOf(CompoundCurve::class, $mutable);
        self::assertSame([[[0, null]]], $immutable->toArray());
        self::assertSame([[[0, null]]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $immutable->getType());
        self::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Geography Collection Creates Both Representations. */
    public function testGeographyCollectionCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = GeographyCollectionFactory::create(elements: [PointFactory::create(x: 0, y: null, srid: $reference)], srid: $reference);
        $emptyImmutable = GeographyCollectionFactory::create(elements: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = GeographyCollectionFactory::create(elements: [PointFactory::create(x: 0, y: null, srid: $reference)], srid: $reference);
        $emptyMutable = GeographyCollectionFactory::create(elements: [], srid: $reference);

        self::assertInstanceOf(GeographyCollectionImmutable::class, $immutable);
        self::assertInstanceOf(SpatialCollection::class, $mutable);
        self::assertSame([[0, null]], $immutable->toArray());
        self::assertSame([[0, null]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $immutable->getType());
        self::assertSame(GeometryTypeEnum::GEOMETRYCOLLECTION, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Line String Creates Both Representations. */
    public function testLineStringCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = LineStringFactory::create(points: [[0, null]], srid: $reference);
        $emptyImmutable = LineStringFactory::create(points: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = LineStringFactory::create(points: [[0, null]], srid: $reference);
        $emptyMutable = LineStringFactory::create(points: [], srid: $reference);

        self::assertInstanceOf(LineStringImmutable::class, $immutable);
        self::assertInstanceOf(LineString::class, $mutable);
        self::assertSame([[0, null]], $immutable->toArray());
        self::assertSame([[0, null]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::LINESTRING, $immutable->getType());
        self::assertSame(GeometryTypeEnum::LINESTRING, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Multi Line String Creates Both Representations. */
    public function testMultiLineStringCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = MultiLineStringFactory::create(lineStrings: [[[0, null]]], srid: $reference);
        $emptyImmutable = MultiLineStringFactory::create(lineStrings: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = MultiLineStringFactory::create(lineStrings: [[[0, null]]], srid: $reference);
        $emptyMutable = MultiLineStringFactory::create(lineStrings: [], srid: $reference);

        self::assertInstanceOf(MultiLineStringImmutable::class, $immutable);
        self::assertInstanceOf(MultiLineString::class, $mutable);
        self::assertSame([[[0, null]]], $immutable->toArray());
        self::assertSame([[[0, null]]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::MULTILINESTRING, $immutable->getType());
        self::assertSame(GeometryTypeEnum::MULTILINESTRING, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Multi Point Creates Both Representations. */
    public function testMultiPointCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = MultiPointFactory::create(points: [[0, null]], srid: $reference);
        $emptyImmutable = MultiPointFactory::create(points: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = MultiPointFactory::create(points: [[0, null]], srid: $reference);
        $emptyMutable = MultiPointFactory::create(points: [], srid: $reference);

        self::assertInstanceOf(MultiPointImmutable::class, $immutable);
        self::assertInstanceOf(MultiPoint::class, $mutable);
        self::assertSame([[0, null]], $immutable->toArray());
        self::assertSame([[0, null]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::MULTIPOINT, $immutable->getType());
        self::assertSame(GeometryTypeEnum::MULTIPOINT, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Multi Polygon Creates Both Representations. */
    public function testMultiPolygonCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = MultiPolygonFactory::create(polygons: [[[[0, null]]]], srid: $reference);
        $emptyImmutable = MultiPolygonFactory::create(polygons: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = MultiPolygonFactory::create(polygons: [[[[0, null]]]], srid: $reference);
        $emptyMutable = MultiPolygonFactory::create(polygons: [], srid: $reference);

        self::assertInstanceOf(MultiPolygonImmutable::class, $immutable);
        self::assertInstanceOf(MultiPolygon::class, $mutable);
        self::assertSame([[[[0, null]]]], $immutable->toArray());
        self::assertSame([[[[0, null]]]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::MULTIPOLYGON, $immutable->getType());
        self::assertSame(GeometryTypeEnum::MULTIPOLYGON, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Point Creates Both Representations. */
    public function testPointCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = PointFactory::create(x: 0, y: null, srid: $reference);
        $emptyImmutable = PointFactory::create(srid: $reference);
        SpatialFactory::mutable();
        $mutable = PointFactory::create(x: 0, y: null, srid: $reference);
        $emptyMutable = PointFactory::create(srid: $reference);

        self::assertInstanceOf(PointImmutable::class, $immutable);
        self::assertInstanceOf(Point::class, $mutable);
        self::assertSame([0, null], $immutable->toArray());
        self::assertSame([0, null], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::POINT, $immutable->getType());
        self::assertSame(GeometryTypeEnum::POINT, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Polygon Creates Both Representations. */
    public function testPolygonCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = PolygonFactory::create(rings: [[[0, null]]], srid: $reference);
        $emptyImmutable = PolygonFactory::create(rings: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = PolygonFactory::create(rings: [[[0, null]]], srid: $reference);
        $emptyMutable = PolygonFactory::create(rings: [], srid: $reference);

        self::assertInstanceOf(PolygonImmutable::class, $immutable);
        self::assertInstanceOf(Polygon::class, $mutable);
        self::assertSame([[[0, null]]], $immutable->toArray());
        self::assertSame([[[0, null]]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::POLYGON, $immutable->getType());
        self::assertSame(GeometryTypeEnum::POLYGON, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }

    /** Triangle Creates Both Representations. */
    public function testTriangleCreatesBothRepresentations(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = TriangleFactory::create(rings: [[[0, null]]], srid: $reference);
        $emptyImmutable = TriangleFactory::create(rings: [], srid: $reference);
        SpatialFactory::mutable();
        $mutable = TriangleFactory::create(rings: [[[0, null]]], srid: $reference);
        $emptyMutable = TriangleFactory::create(rings: [], srid: $reference);

        self::assertInstanceOf(TriangleImmutable::class, $immutable);
        self::assertInstanceOf(Triangle::class, $mutable);
        self::assertSame([[[0, null]]], $immutable->toArray());
        self::assertSame([[[0, null]]], $mutable->toArray());
        self::assertSame([], $emptyImmutable->toArray());
        self::assertSame([], $emptyMutable->toArray());
        self::assertTrue($emptyImmutable->isEmpty());
        self::assertTrue($emptyMutable->isEmpty());
        self::assertSame(CoordinateDimensionEnum::XY, $immutable->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $mutable->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $immutable->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $mutable->getFamily());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $immutable->getType());
        self::assertSame(GeometryTypeEnum::TRIANGLE, $mutable->getType());
        self::assertSame($reference, $immutable->getSpatialReference());
        self::assertSame($reference, $mutable->getSpatialReference());
        self::assertSame($reference, $emptyImmutable->getSpatialReference());
        self::assertSame($reference, $emptyMutable->getSpatialReference());
    }
}
