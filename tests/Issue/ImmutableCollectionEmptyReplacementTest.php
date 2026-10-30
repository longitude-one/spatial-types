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

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PolygonImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/** Immutable replacements preserve originals while updating collection emptiness.
 * @internal
 *
 * @coversNothing
 */
final class ImmutableCollectionEmptyReplacementTest extends TestCase
{
    /** Classification does not enforce family, layout or declared-reference compatibility. */
    public function testImmutableMixedContextMembers(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(null, null, null, 3857);
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\LineStringImmutable([], 2154);
        $empty = new GeometryCollectionImmutable(4326, [$point, $line]);
        $partial = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(null, 2, null, null, 3857);
        $mixed = new GeometryCollectionImmutable(4326, [$point, $partial, $line]);

        self::assertTrue($empty->isEmpty());
        self::assertFalse($mixed->isEmpty());
        self::assertSame([$point, $line], $empty->getElements());
        self::assertSame([$point, $partial, $line], $mixed->getElements());
        self::assertSame([[], [null, 2, null, null], []], $mixed->toArray());
        self::assertSame(3857, $point->getSrid());
        self::assertSame(2154, $line->getSrid());
    }

    /** A line-string replacement changes emptiness without dropping empty neighbours. */
    public function testMultiLineStringReplacement(): void
    {
        $first = new LineStringImmutable([], 4326);
        $second = new LineStringImmutable([], 4326);
        $original = new MultiLineStringImmutable([$first, $second], 4326);
        $filled = $original->withLineString(1, [[0, 0], [1, 1]]);
        $emptied = $filled->withLineString(1, []);

        self::assertTrue($original->isEmpty());
        self::assertFalse($filled->isEmpty());
        self::assertTrue($emptied->isEmpty());
        self::assertSame([[], []], $original->toArray());
        self::assertSame([[], [[0, 0], [1, 1]]], $filled->toArray());
        self::assertSame([[], []], $emptied->toArray());
        self::assertSame([$first, $second], $original->getElements());
        self::assertNotSame($first, $filled->getLineString(0));
        self::assertSame(4326, $filled->getSrid());
    }

    /** Filling and emptying a point never removes its stored member position. */
    public function testMultiPointReplacement(): void
    {
        $first = new PointImmutable(null, null, 4326);
        $second = new PointImmutable(null, null, 4326);
        $original = new MultiPointImmutable([$first, $second], 4326);
        $filled = $original->withPoint(1, Coordinates::xy(1, null));
        $emptied = $filled->withPoint(1, Coordinates::xy(null, null));
        $referenceCopy = $original->withSrid(3857);

        self::assertTrue($original->isEmpty());
        self::assertFalse($filled->isEmpty());
        self::assertTrue($emptied->isEmpty());
        self::assertTrue($referenceCopy->isEmpty());
        self::assertSame([[], []], $original->toArray());
        self::assertSame([[], [1, null]], $filled->toArray());
        self::assertSame([[], []], $emptied->toArray());
        self::assertSame([$first, $second], $original->getElements());
        self::assertNotSame($first, $filled->getPoint(0));
        self::assertNotSame($second, $filled->getPoint(1));
        self::assertSame(4326, $first->getSrid());
        self::assertSame(3857, $referenceCopy->getPoint(1)->getSrid());
    }

    /** A polygon replacement changes emptiness without dropping empty neighbours. */
    public function testMultiPolygonReplacement(): void
    {
        $first = new PolygonImmutable([], 4326);
        $second = new PolygonImmutable([], 4326);
        $original = new MultiPolygonImmutable([$first, $second], 4326);
        $filled = $original->withPolygon(1, [[[0, 0], [2, 0], [0, 2], [0, 0]]]);
        $emptied = $filled->withPolygon(1, []);

        self::assertTrue($original->isEmpty());
        self::assertFalse($filled->isEmpty());
        self::assertTrue($emptied->isEmpty());
        self::assertSame([[], []], $original->toArray());
        self::assertSame([[], [[[0, 0], [2, 0], [0, 2], [0, 0]]]], $filled->toArray());
        self::assertSame([[], []], $emptied->toArray());
        self::assertSame([$first, $second], $original->getElements());
        self::assertNotSame($first, $filled->getPolygon(0));
        self::assertSame(4326, $filled->getSrid());
    }

    /** Nested replacement propagates EMPTY classification without modifying the source graph. */
    public function testNestedGeometryCollectionReplacement(): void
    {
        $point = new PointImmutable(null, null, 4326);
        $members = new MultiPointImmutable([$point], 4326);
        $inner = new GeometryCollectionImmutable(4326, [$members]);
        $original = new GeometryCollectionImmutable(4326, [$inner, $point]);
        $populatedInner = $inner->withElement(0, new PointImmutable(1, 2, 4326));
        $filled = $original->withElement(0, $populatedInner);
        $emptied = $filled->withElement(0, $inner);
        $copied = $original->withSrid(3857);

        self::assertTrue($original->isEmpty());
        self::assertTrue($copied->isEmpty());
        self::assertFalse($filled->isEmpty());
        self::assertTrue($emptied->isEmpty());
        self::assertSame([[[[]]], []], $original->toArray());
        self::assertSame([[[1, 2]], []], $filled->toArray());
        self::assertSame([[[[]]], []], $emptied->toArray());
        self::assertSame([$inner, $point], $original->getElements());
        self::assertSame([$members], $inner->getElements());
        self::assertSame([$point], $members->getElements());
        self::assertNotSame($point, $filled->getElements()[1]);
        self::assertSame(4326, $point->getSrid());
        self::assertSame(3857, $copied->getSrid());
    }

    /** A stored EMPTY point does not redefine line or polygon EMPTY semantics. */
    public function testNonCollectionMemberContractsRemainUnchanged(): void
    {
        $point = new PointImmutable();
        $line = new LineStringImmutable([$point]);
        $polygon = new PolygonImmutable([[]]);
        $lines = new MultiLineStringImmutable([$line]);
        $polygons = new MultiPolygonImmutable([$polygon]);
        $collection = new GeometryCollectionImmutable(0, [$line, $polygon]);

        self::assertFalse($line->isEmpty());
        self::assertFalse($polygon->isEmpty());
        self::assertFalse($lines->isEmpty());
        self::assertFalse($polygons->isEmpty());
        self::assertFalse($collection->isEmpty());
        self::assertSame([$point], $line->getElements());
        self::assertSame([[]], $polygon->toArray());
    }
}
