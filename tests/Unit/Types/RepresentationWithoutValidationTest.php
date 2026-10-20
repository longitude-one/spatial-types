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

use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollection;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Polygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Triangle;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolyhedralSurface;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RepresentationWithoutValidationTest extends TestCase
{
    /** Circular string stores incomplete arcs and empty members. */
    public function testCircularStringStoresIncompleteArcsAndEmptyMembers(): void
    {
        $curve = new CircularString([new Point(), new Point(1, 1)]);
        $copy = $curve->withArrayOfCoordinates([[2, 2]]);

        self::assertSame([[], [1, 1]], $curve->toArray());
        self::assertSame([[2, 2]], $copy->toArray());
    }

    /** Compound curve stores disconnected and empty components. */
    public function testCompoundCurveStoresDisconnectedAndEmptyComponents(): void
    {
        $curve = new CompoundCurve([
            new LineString([[0, 0], [1, 1]]),
            new CircularString([[5, 5]]),
            new LineString([]),
        ]);
        $copy = $curve->withSrid(4326);

        self::assertSame([[[0, 0], [1, 1]], [[5, 5]], []], $curve->toArray());
        self::assertSame([[[0, 0], [1, 1]], [[5, 5]], []], $copy->toArray());
        self::assertSame(0, $curve->getCurve(0)->getSrid());
        self::assertSame(4326, $copy->getCurve(0)->getSrid());
        self::assertNull($copy->getEndPoint());
    }

    /** Empty ring still rejects indexed replacement. */
    public function testEmptyRingStillRejectsIndexedReplacement(): void
    {
        $polygon = new Polygon([[]]);
        $this->expectException(OutOfBoundsException::class);

        $polygon->withPoint(0, 0, Coordinates::xy(1, 2));
    }

    /** Membership does not enforce family dimension or reference. */
    public function testMembershipDoesNotEnforceFamilyDimensionOrReference(): void
    {
        $member = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point(1, 2, 3, SpatialReference::epsg(4326));
        $collection = new GeometryCollection(2154, [$member, new GeometryCollection()]);
        $copy = $collection->withElement(1, new Point(4, 5, 3857));

        self::assertSame($member, $collection->getElements()[0]);
        self::assertSame(4326, $collection->getElements()[0]->getSrid());
        self::assertSame(2154, $collection->getSrid());
        self::assertSame(3857, $copy->getElements()[1]->getSrid());
        self::assertInstanceOf(GeometryCollection::class, $collection->getElements()[1]);
    }

    /** Spatial reference retains unvalidated identifiers and authorities. */
    public function testSpatialReferenceRetainsUnvalidatedIdentifiersAndAuthorities(): void
    {
        $reference = new SpatialReference(-1, '');
        $point = new Point(1, 2, $reference);

        self::assertSame(-1, $point->getSrid());
        self::assertSame('', $point->getSpatialReference()->authority);
        self::assertSame($reference, $point->getSpatialReference());
    }

    /** Xy duplicate replacement preserves original. */
    public function testXyDuplicateReplacementPreservesOriginal(): void
    {
        $line = new LineString([[0, 0], [1, 1]], 4326);
        $copy = $line->withPoint(1, Coordinates::xy(0, 0));

        self::assertSame([[0, 0], [0, 0]], $copy->toArray());
        self::assertSame([[0, 0], [1, 1]], $line->toArray());
        self::assertSame(4326, $copy->getSrid());
        self::assertSame(LineString::class, $copy::class);
    }

    /** Xym triangle stores open boundaries and holes. */
    public function testXymTriangleStoresOpenBoundariesAndHoles(): void
    {
        $triangle = new Triangle([[[0, 0, 7], [1, 0, 8]], [[2, 2, 9]]]);
        $copy = $triangle->withArrayOfCoordinates([[[3, 3, 10]]]);

        self::assertSame([[[0, 0, 7], [1, 0, 8]], [[2, 2, 9]]], $triangle->toArray());
        self::assertSame([[[3, 3, 10]]], $copy->toArray());
        self::assertSame(Triangle::class, $copy::class);
        self::assertTrue($copy->hasM());
        self::assertFalse($copy->hasZ());
    }

    /** Xyz geography stores numeric ranges without validation. */
    public function testXyzGeographyStoresNumericRangesWithoutValidation(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\Point(181, 91, 5, 4326);
        $copy = $point->withCoordinates(Coordinates::xyz(-181, -91, 6));

        self::assertSame([181, 91, 5], $point->toArray());
        self::assertSame([-181, -91, 6], $copy->toArray());
        self::assertSame(4326, $copy->getSrid());
    }

    /** Xyzm geography stores open polygon replacement. */
    public function testXyzmGeographyStoresOpenPolygonReplacement(): void
    {
        $polygon = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\Polygon([[[181, 91, 5, 7]]], 4326);
        $copy = $polygon->withRing(0, [[182, 92, 6, 8], [183, 93, 7, 9]]);

        self::assertSame([[[181, 91, 5, 7]]], $polygon->toArray());
        self::assertSame([[[182, 92, 6, 8], [183, 93, 7, 9]]], $copy->toArray());
        self::assertTrue($copy->hasZ());
        self::assertTrue($copy->hasM());
        self::assertSame(4326, $copy->getSrid());
    }

    /** Xyzm surface stores unclosed patch replacement. */
    public function testXyzmSurfaceStoresUnclosedPatchReplacement(): void
    {
        $surface = new PolyhedralSurface([[[[0, 0, 0, 7]]]], 4326);
        $copy = $surface->withRing(0, 0, [[181, 91, 1, 8], [182, 92, 2, 9]]);

        self::assertSame([[[[0, 0, 0, 7]]]], $surface->toArray());
        self::assertSame([[[[181, 91, 1, 8], [182, 92, 2, 9]]]], $copy->toArray());
        self::assertSame(4326, $copy->getSrid());
    }

    /** Xyz surface stores disconnected and empty patches. */
    public function testXyzSurfaceStoresDisconnectedAndEmptyPatches(): void
    {
        $surface = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PolyhedralSurface([
            [[[0, 0, 0], [1, 0, 0]]],
            [[[10, 10, 10]]],
            [],
        ]);
        $copy = $surface->withPatch(0, [[[4, 4, 4]]]);

        self::assertSame([[[[0, 0, 0], [1, 0, 0]]], [[[10, 10, 10]]], []], $surface->toArray());
        self::assertSame([[[[4, 4, 4]]], [[[10, 10, 10]]], []], $copy->toArray());
    }
}
