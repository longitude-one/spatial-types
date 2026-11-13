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

use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\LineStringImmutable as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiLineStringImmutable as GeographicMultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiLineStringImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Issue #9: empty line-string members in Dimension3z.
 *
 * @internal
 *
 * @coversNothing
 */
class EmptyMultiLineStringDimension3zTest extends TestCase
{
    /** Coordinate arrays create empty members in the receiving context. */
    public function testGeographyCoordinateArrays(): void
    {
        $mixed = new GeographicMultiLineString([[], [[1, 2, 3], [4, 5, 6]], [], [[7, 8, 9], [10, 11, 12]], []], 4326);
        $single = new GeographicMultiLineString([[]], 4326);

        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], [], [[7, 8, 9], [10, 11, 12]], []], $mixed->toArray());
        self::assertCount(5, $mixed->getElements());
        self::assertInstanceOf(GeographicLineString::class, $mixed->getLineString(0));
        self::assertSame([], $mixed->getLineString(0)->toArray());
        self::assertSame([], $mixed->getLineString(2)->toArray());
        self::assertSame([], $mixed->getLineString(4)->toArray());
        self::assertTrue($mixed->getLineString(0)->hasZ());
        self::assertFalse($mixed->getLineString(0)->hasM());
        self::assertSame(4326, $mixed->getLineString(0)->getSrid());
        self::assertSame([[]], $single->toArray());
        self::assertCount(1, $single->getElements());
    }

    /** Replacement keeps untouched empty members and can empty or fill a member. */
    public function testGeographyLineStringReplacement(): void
    {
        $original = new GeographicMultiLineString([[], [[1, 2, 3], [4, 5, 6]], []], 4326);
        $emptied = $original->withLineString(1, []);
        $filled = $original->withLineString(-1, [[1, 2, 3], [4, 5, 6]]);

        self::assertSame([[], [], []], $emptied->toArray());
        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], [[1, 2, 3], [4, 5, 6]]], $filled->toArray());
        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], []], $original->toArray());
        self::assertCount(3, $emptied->getElements());
        self::assertCount(3, $filled->getElements());
        self::assertNotSame($original, $emptied);
        self::assertNotSame($original->getLineString(0), $emptied->getLineString(0));
        self::assertNotSame($original->getLineString(2), $emptied->getLineString(2));
        self::assertNotSame($original->getLineString(1), $filled->getLineString(1));
        self::assertNotSame($original->getLineString(1)->getPoint(0), $filled->getLineString(1)->getPoint(0));
        self::assertInstanceOf(GeographicLineString::class, $emptied->getLineString(1));
        self::assertSame(4326, $emptied->getLineString(1)->getSrid());
        self::assertTrue($emptied->getLineString(1)->hasZ());
        self::assertFalse($emptied->getLineString(1)->hasM());
    }

    /** Object members retain their count, order and accessors. */
    public function testGeographyObjectMembers(): void
    {
        $empty = new GeographicLineString([], 4326);
        $line = new GeographicLineString([[1, 2, 3], [4, 5, 6]], 4326);
        $otherLine = new GeographicLineString([[7, 8, 9], [10, 11, 12]], 4326);
        $mixed = new GeographicMultiLineString([$empty, $line, new GeographicLineString([], 4326), $otherLine, $empty], 4326);
        $single = new GeographicMultiLineString([$empty], 4326);
        $zero = new GeographicMultiLineString([], 4326);

        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], [], [[7, 8, 9], [10, 11, 12]], []], $mixed->toArray());
        self::assertCount(5, $mixed->getElements());
        self::assertSame($mixed->getLineStrings(), $mixed->getElements());
        self::assertSame($empty, $mixed->getLineString(0));
        self::assertSame($line, $mixed->getLineString(1));
        self::assertSame($otherLine, $mixed->getLineString(3));
        self::assertSame($empty, $mixed->getLineString(-1));
        self::assertSame($empty, $mixed->getLineString(5));
        self::assertSame([$empty], $single->getElements());
        self::assertCount(1, $single->getLineStrings());
        self::assertSame([[]], $single->toArray());
        self::assertSame([], $zero->getElements());
        self::assertCount(0, $zero->getLineStrings());
        self::assertSame([], $zero->toArray());
        self::assertTrue($empty->hasZ());
        self::assertFalse($empty->hasM());
        self::assertSame(4326, $empty->getSrid());
    }

    /** Reference copies preserve empty positions, ordinates and the source graph. */
    public function testGeographyReferenceCopies(): void
    {
        $original = new GeographicMultiLineString([[], [[1, 2, 3], [4, 5, 6]], []], 4326);
        $reference = SpatialReference::epsg(2154);
        $referenced = $original->withSpatialReference($reference);
        $renumbered = $original->withSrid(3857);

        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], []], $referenced->toArray());
        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], []], $renumbered->toArray());
        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], []], $original->toArray());
        self::assertCount(3, $referenced->getElements());
        self::assertCount(3, $renumbered->getElements());
        self::assertSame($reference, $referenced->getSpatialReference());
        self::assertSame($reference, $referenced->getLineString(0)->getSpatialReference());
        self::assertSame($reference, $referenced->getLineString(1)->getPoint(0)->getSpatialReference());
        self::assertSame($reference, $referenced->getLineString(2)->getSpatialReference());
        self::assertSame(3857, $renumbered->getLineString(0)->getSrid());
        self::assertSame(3857, $renumbered->getLineString(1)->getPoint(0)->getSrid());
        self::assertSame(3857, $renumbered->getLineString(2)->getSrid());
        self::assertSame(4326, $original->getLineString(0)->getSrid());
        self::assertSame(4326, $original->getLineString(1)->getPoint(0)->getSrid());
        self::assertSame(4326, $original->getLineString(2)->getSrid());
        self::assertNotSame($original->getLineString(0), $referenced->getLineString(0));
        self::assertNotSame($original->getLineString(2), $renumbered->getLineString(2));
        self::assertNotSame($original->getLineString(1)->getPoint(0), $referenced->getLineString(1)->getPoint(0));
        self::assertNotSame($original->getLineString(1)->getPoint(0), $renumbered->getLineString(1)->getPoint(0));
        self::assertInstanceOf(GeographicLineString::class, $referenced->getLineString(0));
        self::assertInstanceOf(GeographicLineString::class, $renumbered->getLineString(2));
        self::assertTrue($referenced->getLineString(0)->hasZ());
        self::assertFalse($renumbered->getLineString(2)->hasM());
    }

    /** Coordinate arrays create empty members in the receiving context. */
    public function testGeometryCoordinateArrays(): void
    {
        $mixed = new MultiLineStringImmutable([[], [[1, 2, 3], [4, 5, 6]], [], [[7, 8, 9], [10, 11, 12]], []], 4326);
        $single = new MultiLineStringImmutable([[]], 4326);

        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], [], [[7, 8, 9], [10, 11, 12]], []], $mixed->toArray());
        self::assertCount(5, $mixed->getElements());
        self::assertInstanceOf(LineStringImmutable::class, $mixed->getLineString(0));
        self::assertSame([], $mixed->getLineString(0)->toArray());
        self::assertSame([], $mixed->getLineString(2)->toArray());
        self::assertSame([], $mixed->getLineString(4)->toArray());
        self::assertTrue($mixed->getLineString(0)->hasZ());
        self::assertFalse($mixed->getLineString(0)->hasM());
        self::assertSame(4326, $mixed->getLineString(0)->getSrid());
        self::assertSame([[]], $single->toArray());
        self::assertCount(1, $single->getElements());
    }

    /** Replacement keeps untouched empty members and can empty or fill a member. */
    public function testGeometryLineStringReplacement(): void
    {
        $original = new MultiLineStringImmutable([[], [[1, 2, 3], [4, 5, 6]], []], 4326);
        $emptied = $original->withLineString(1, []);
        $filled = $original->withLineString(-1, [[1, 2, 3], [4, 5, 6]]);

        self::assertSame([[], [], []], $emptied->toArray());
        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], [[1, 2, 3], [4, 5, 6]]], $filled->toArray());
        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], []], $original->toArray());
        self::assertCount(3, $emptied->getElements());
        self::assertCount(3, $filled->getElements());
        self::assertNotSame($original, $emptied);
        self::assertNotSame($original->getLineString(0), $emptied->getLineString(0));
        self::assertNotSame($original->getLineString(2), $emptied->getLineString(2));
        self::assertNotSame($original->getLineString(1), $filled->getLineString(1));
        self::assertNotSame($original->getLineString(1)->getPoint(0), $filled->getLineString(1)->getPoint(0));
        self::assertInstanceOf(LineStringImmutable::class, $emptied->getLineString(1));
        self::assertSame(4326, $emptied->getLineString(1)->getSrid());
        self::assertTrue($emptied->getLineString(1)->hasZ());
        self::assertFalse($emptied->getLineString(1)->hasM());
    }

    /** Object members retain their count, order and accessors. */
    public function testGeometryObjectMembers(): void
    {
        $empty = new LineStringImmutable([], 4326);
        $line = new LineStringImmutable([[1, 2, 3], [4, 5, 6]], 4326);
        $otherLine = new LineStringImmutable([[7, 8, 9], [10, 11, 12]], 4326);
        $mixed = new MultiLineStringImmutable([$empty, $line, new LineStringImmutable([], 4326), $otherLine, $empty], 4326);
        $single = new MultiLineStringImmutable([$empty], 4326);
        $zero = new MultiLineStringImmutable([], 4326);

        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], [], [[7, 8, 9], [10, 11, 12]], []], $mixed->toArray());
        self::assertCount(5, $mixed->getElements());
        self::assertSame($mixed->getLineStrings(), $mixed->getElements());
        self::assertSame($empty, $mixed->getLineString(0));
        self::assertSame($line, $mixed->getLineString(1));
        self::assertSame($otherLine, $mixed->getLineString(3));
        self::assertSame($empty, $mixed->getLineString(-1));
        self::assertSame($empty, $mixed->getLineString(5));
        self::assertSame([$empty], $single->getElements());
        self::assertCount(1, $single->getLineStrings());
        self::assertSame([[]], $single->toArray());
        self::assertSame([], $zero->getElements());
        self::assertCount(0, $zero->getLineStrings());
        self::assertSame([], $zero->toArray());
        self::assertTrue($empty->hasZ());
        self::assertFalse($empty->hasM());
        self::assertSame(4326, $empty->getSrid());
    }

    /** Reference copies preserve empty positions, ordinates and the source graph. */
    public function testGeometryReferenceCopies(): void
    {
        $original = new MultiLineStringImmutable([[], [[1, 2, 3], [4, 5, 6]], []], 4326);
        $reference = SpatialReference::epsg(2154);
        $referenced = $original->withSpatialReference($reference);
        $renumbered = $original->withSrid(3857);

        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], []], $referenced->toArray());
        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], []], $renumbered->toArray());
        self::assertSame([[], [[1, 2, 3], [4, 5, 6]], []], $original->toArray());
        self::assertCount(3, $referenced->getElements());
        self::assertCount(3, $renumbered->getElements());
        self::assertSame($reference, $referenced->getSpatialReference());
        self::assertSame($reference, $referenced->getLineString(0)->getSpatialReference());
        self::assertSame($reference, $referenced->getLineString(1)->getPoint(0)->getSpatialReference());
        self::assertSame($reference, $referenced->getLineString(2)->getSpatialReference());
        self::assertSame(3857, $renumbered->getLineString(0)->getSrid());
        self::assertSame(3857, $renumbered->getLineString(1)->getPoint(0)->getSrid());
        self::assertSame(3857, $renumbered->getLineString(2)->getSrid());
        self::assertSame(4326, $original->getLineString(0)->getSrid());
        self::assertSame(4326, $original->getLineString(1)->getPoint(0)->getSrid());
        self::assertSame(4326, $original->getLineString(2)->getSrid());
        self::assertNotSame($original->getLineString(0), $referenced->getLineString(0));
        self::assertNotSame($original->getLineString(2), $renumbered->getLineString(2));
        self::assertNotSame($original->getLineString(1)->getPoint(0), $referenced->getLineString(1)->getPoint(0));
        self::assertNotSame($original->getLineString(1)->getPoint(0), $renumbered->getLineString(1)->getPoint(0));
        self::assertInstanceOf(LineStringImmutable::class, $referenced->getLineString(0));
        self::assertInstanceOf(LineStringImmutable::class, $renumbered->getLineString(2));
        self::assertTrue($referenced->getLineString(0)->hasZ());
        self::assertFalse($renumbered->getLineString(2)->hasM());
    }
}
