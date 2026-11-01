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
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geometry\LineStringFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geometry\MultiPointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geometry\PointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension2\Geometry\PolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension3z\Geography\PointFactory as GeographicPointFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\MultiPolygonFactory;
use LongitudeOne\SpatialTypes\Factory\Dimension4zm\Geography\PolyhedralSurfaceFactory;
use LongitudeOne\SpatialTypes\Factory\SpatialFactory;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableLineStringInterface;
use LongitudeOne\SpatialTypes\Interfaces\ImmutableSpatialInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableLineStringInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable as GeographicPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Point as GeographicPoint;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Polygon;
use PHPUnit\Framework\TestCase;

/** Constructor delegation preserves component ownership and technical representation.
 * @internal
 *
 * @coversNothing
 */
final class FactoryOwnershipTest extends TestCase
{
    /** Restore the shared mode for subsequent tests. */
    protected function tearDown(): void
    {
        SpatialFactory::immutable();
        parent::tearDown();
    }

    /** Immutable Creation Retains Supplied Point Without Relabelling It. */
    public function testImmutableCreationRetainsSuppliedPointWithoutRelabellingIt(): void
    {
        SpatialFactory::immutable();
        $point = PointFactory::create(0, null, SpatialReference::epsg(4326));
        $line = LineStringFactory::create([$point], SpatialReference::epsg(2154));
        $multiPoint = MultiPointFactory::create([$point], 3857);

        static::assertSame($point, $line->getPoint(0));
        static::assertSame($point, $multiPoint->getElements()[0]);
        static::assertSame(4326, $point->getSrid());
        static::assertSame(2154, $line->getSrid());
        static::assertSame([[0, null]], $line->toArray());
        static::assertInstanceOf(ImmutableSpatialInterface::class, $line);
    }

    /** Immutable Factory Preserves Constructor Coordinate Parsing. */
    public function testImmutableFactoryPreservesConstructorCoordinateParsing(): void
    {
        SpatialFactory::immutable();
        $point = GeographicPointFactory::create("2°30'E", "48°30'N", null, 4326);
        static::assertSame([2.5, 48.5, null], $point->toArray());
    }

    /** Immutable Factory Rejects Unsupported Coordinate Layout Without Changing Mode. */
    public function testImmutableFactoryRejectsUnsupportedCoordinateLayoutWithoutChangingMode(): void
    {
        SpatialFactory::immutable();

        try {
            LineStringFactory::create([[0, 1, 2]]);
            static::fail('XY construction must reject an XYZ tuple.');
        } catch (InvalidDimensionException) {
            static::assertFalse(SpatialFactory::isMutable());
        }
    }

    /** Immutable Mode Rejects An Earlier Mutable Point Without Changing It. */
    public function testImmutableModeRejectsAnEarlierMutablePointWithoutChangingIt(): void
    {
        SpatialFactory::mutable();
        $point = PointFactory::create(0, null, 4326);
        SpatialFactory::immutable();

        try {
            LineStringFactory::create([$point], 2154);
            static::fail('Mutable components must be rejected.');
        } catch (InvalidValueException) {
            static::assertInstanceOf(Point::class, $point);
            static::assertSame([0, null], $point->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** Immutable Mode Rejects Mutable Descendants Of Custom Immutable Components. */
    public function testImmutableModeRejectsMutableDescendantsOfCustomImmutableComponents(): void
    {
        SpatialFactory::mutable();
        $point = PointFactory::create(0, null, 4326);
        $ring = static::createStub(ImmutableLineStringInterface::class);
        $ring->method('getStartPoint')->willReturn(null);
        $ring->method('getEndPoint')->willReturn(null);
        $ring->method('getElements')->willReturn([$point]);
        $ring->method('getPoints')->willReturn([$point]);
        SpatialFactory::immutable();

        $this->expectException(InvalidValueException::class);
        PolygonFactory::create([$ring]);
    }

    /** Immutable Mode Rejects Mutable Points Inside Nested Arrays. */
    public function testImmutableModeRejectsMutablePointsInsideNestedArrays(): void
    {
        SpatialFactory::mutable();
        $point = PointFactory::create(0, null, 4326);
        SpatialFactory::immutable();
        $this->expectException(InvalidValueException::class);
        PolygonFactory::create([[$point]], 2154);
    }

    /** Mutable Creation Retains And Shares Supplied Point. */
    public function testMutableCreationRetainsAndSharesSuppliedPoint(): void
    {
        SpatialFactory::mutable();
        $point = PointFactory::create(0, null, SpatialReference::epsg(4326));
        $line = LineStringFactory::create([$point], SpatialReference::epsg(2154));
        $multiPoint = MultiPointFactory::create([$point], 3857);

        static::assertInstanceOf(Point::class, $point);
        static::assertSame($point, $line->getPoint(0));
        static::assertSame($point, $multiPoint->getElements()[0]);
        static::assertSame(4326, $point->getSrid());
        $point->setX(8);
        static::assertSame([[8, null]], $line->toArray());
        static::assertSame([[8, null]], $multiPoint->toArray());
    }

    /** Mutable Factory Preserves Constructor Coordinate Parsing. */
    public function testMutableFactoryPreservesConstructorCoordinateParsing(): void
    {
        SpatialFactory::mutable();
        $point = GeographicPointFactory::create("2°30'E", "48°30'N", null, 4326);
        static::assertSame([2.5, 48.5, null], $point->toArray());
    }

    /** Mutable Factory Rejects Unsupported Coordinate Layout Without Changing Mode. */
    public function testMutableFactoryRejectsUnsupportedCoordinateLayoutWithoutChangingMode(): void
    {
        SpatialFactory::mutable();

        try {
            LineStringFactory::create([[0, 1, 2]]);
            static::fail('XY construction must reject an XYZ tuple.');
        } catch (InvalidDimensionException) {
            static::assertTrue(SpatialFactory::isMutable());
        }
    }

    /** Mutable Mode Rejects An Earlier Immutable Point Without Changing It. */
    public function testMutableModeRejectsAnEarlierImmutablePointWithoutChangingIt(): void
    {
        SpatialFactory::immutable();
        $point = PointFactory::create(0, null, 4326);
        SpatialFactory::mutable();

        try {
            LineStringFactory::create([$point], 2154);
            static::fail('Immutable components must be rejected.');
        } catch (InvalidValueException) {
            static::assertInstanceOf(PointImmutable::class, $point);
            static::assertSame([0, null], $point->toArray());
            static::assertSame(4326, $point->getSrid());
        }
    }

    /** Mutable Mode Rejects Immutable Descendants Of Custom Mutable Components. */
    public function testMutableModeRejectsImmutableDescendantsOfCustomMutableComponents(): void
    {
        SpatialFactory::immutable();
        $point = PointFactory::create(0, null, 4326);
        $ring = static::createStub(MutableLineStringInterface::class);
        $ring->method('getStartPoint')->willReturn(null);
        $ring->method('getEndPoint')->willReturn(null);
        $ring->method('getElements')->willReturn([$point]);
        $ring->method('getPoints')->willReturn([$point]);
        SpatialFactory::mutable();

        $this->expectException(InvalidValueException::class);
        PolygonFactory::create([$ring]);
    }

    /** Mutable Mode Rejects Immutable Points Inside Nested Arrays. */
    public function testMutableModeRejectsImmutablePointsInsideNestedArrays(): void
    {
        SpatialFactory::immutable();
        $point = PointFactory::create(0, null, 4326);
        SpatialFactory::mutable();
        $this->expectException(InvalidValueException::class);
        PolygonFactory::create([[$point]], 2154);
    }

    /** Nested Immutable Arrays Create Only Immutable Descendants In Declared Context. */
    public function testNestedImmutableArraysCreateOnlyImmutableDescendantsInDeclaredContext(): void
    {
        SpatialFactory::immutable();
        $reference = SpatialReference::epsg(4326);
        $multi = MultiPolygonFactory::create([[[[0, null, 12, 7]]], []], $reference);
        $polygon = $multi->getPolygons()[0];
        $ring = $polygon->getRing(0);
        $point = $ring->getPoint(0);

        static::assertInstanceOf(PolygonImmutable::class, $polygon);
        static::assertInstanceOf(LineStringImmutable::class, $ring);
        static::assertInstanceOf(GeographicPointImmutable::class, $point);
        static::assertSame([[[[0, null, 12, 7]]], []], $multi->toArray());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygon->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $ring->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame($reference, $polygon->getSpatialReference());
        static::assertSame($reference, $ring->getSpatialReference());
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
    }

    /** Nested Mutable Arrays Create Only Mutable Descendants In Declared Context. */
    public function testNestedMutableArraysCreateOnlyMutableDescendantsInDeclaredContext(): void
    {
        SpatialFactory::mutable();
        $reference = SpatialReference::epsg(4326);
        $multi = MultiPolygonFactory::create([[[[0, null, 12, 7]]], []], $reference);
        $polygon = $multi->getPolygons()[0];
        $ring = $polygon->getRing(0);
        $point = $ring->getPoint(0);

        static::assertInstanceOf(Polygon::class, $polygon);
        static::assertInstanceOf(LineString::class, $ring);
        static::assertInstanceOf(GeographicPoint::class, $point);
        static::assertSame([[[[0, null, 12, 7]]], []], $multi->toArray());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $polygon->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $ring->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame($reference, $polygon->getSpatialReference());
        static::assertSame($reference, $ring->getSpatialReference());
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame(CoordinateDimensionEnum::XYZM, $point->getDimension());
        static::assertSame($point, $point->setX(9));
        static::assertSame([[[[9, null, 12, 7]]], []], $multi->toArray());
    }

    /** Nested Surface Arrays Preserve Empty Patches And Null Ordinates In Both Modes. */
    public function testNestedSurfaceArraysPreserveEmptyPatchesAndNullOrdinatesInBothModes(): void
    {
        $reference = SpatialReference::epsg(4326);
        SpatialFactory::immutable();
        $immutable = PolyhedralSurfaceFactory::create([[[[0, null, 12, 7]]], []], $reference);
        SpatialFactory::mutable();
        $mutable = PolyhedralSurfaceFactory::create([[[[0, null, 12, 7]]], []], $reference);

        static::assertInstanceOf(PolygonImmutable::class, $immutable->getPatch(0));
        static::assertInstanceOf(GeographicPointImmutable::class, $immutable->getPatch(0)->getRing(0)->getPoint(0));
        static::assertInstanceOf(Polygon::class, $mutable->getPatch(0));
        static::assertInstanceOf(GeographicPoint::class, $mutable->getPatch(0)->getRing(0)->getPoint(0));
        static::assertSame($reference, $immutable->getPatch(0)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame($reference, $mutable->getPatch(0)->getRing(0)->getPoint(0)->getSpatialReference());
        static::assertSame([[[[0, null, 12, 7]]], []], $immutable->toArray());
        static::assertSame([[[[0, null, 12, 7]]], []], $mutable->toArray());
    }
}
