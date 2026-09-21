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

use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\LineString as GeographicLineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiLineString as GeographicMultiLineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiLineString;
use PHPUnit\Framework\TestCase;

/**
 * Issue #9: empty line-string members in Dimension4zm.
 *
 * @internal
 *
 * @coversNothing
 */
class EmptyMultiLineStringDimension4zmTest extends TestCase
{
    /** Coordinate arrays create empty members in the receiving context. */
    public function testGeographyCoordinateArrays(): void
    {
        $mixed = new GeographicMultiLineString([[], [[1, 2, 3, 7], [4, 5, 6, 8]], [], [[7, 8, 9, 10], [11, 12, 13, 14]], []], 4326);
        $single = new GeographicMultiLineString([[]], 4326);

        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], [], [[7, 8, 9, 10], [11, 12, 13, 14]], []], $mixed->toArray());
        static::assertCount(5, $mixed->getElements());
        static::assertInstanceOf(GeographicLineString::class, $mixed->getLineString(0));
        static::assertSame([], $mixed->getLineString(0)->toArray());
        static::assertSame([], $mixed->getLineString(2)->toArray());
        static::assertSame([], $mixed->getLineString(4)->toArray());
        static::assertTrue($mixed->getLineString(0)->hasZ());
        static::assertTrue($mixed->getLineString(0)->hasM());
        static::assertSame(4326, $mixed->getLineString(0)->getSrid());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
    }

    /** Replacement keeps untouched empty members and can empty or fill a member. */
    public function testGeographyLineStringReplacement(): void
    {
        $original = new GeographicMultiLineString([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], 4326);
        $emptied = $original->withLineString(1, []);
        $filled = $original->withLineString(-1, [[1, 2, 3, 7], [4, 5, 6, 8]]);

        static::assertSame([[], [], []], $emptied->toArray());
        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], [[1, 2, 3, 7], [4, 5, 6, 8]]], $filled->toArray());
        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], $original->toArray());
        static::assertCount(3, $emptied->getElements());
        static::assertCount(3, $filled->getElements());
        static::assertNotSame($original, $emptied);
        static::assertNotSame($original->getLineString(0), $emptied->getLineString(0));
        static::assertNotSame($original->getLineString(2), $emptied->getLineString(2));
        static::assertNotSame($original->getLineString(1), $filled->getLineString(1));
        static::assertNotSame($original->getLineString(1)->getPoint(0), $filled->getLineString(1)->getPoint(0));
        static::assertInstanceOf(GeographicLineString::class, $emptied->getLineString(1));
        static::assertSame(4326, $emptied->getLineString(1)->getSrid());
        static::assertTrue($emptied->getLineString(1)->hasZ());
        static::assertTrue($emptied->getLineString(1)->hasM());
    }

    /** Object members retain their count, order and accessors. */
    public function testGeographyObjectMembers(): void
    {
        $empty = new GeographicLineString([], 4326);
        $line = new GeographicLineString([[1, 2, 3, 7], [4, 5, 6, 8]], 4326);
        $otherLine = new GeographicLineString([[7, 8, 9, 10], [11, 12, 13, 14]], 4326);
        $mixed = new GeographicMultiLineString([$empty, $line, new GeographicLineString([], 4326), $otherLine, $empty], 4326);
        $single = new GeographicMultiLineString([$empty], 4326);
        $zero = new GeographicMultiLineString([], 4326);

        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], [], [[7, 8, 9, 10], [11, 12, 13, 14]], []], $mixed->toArray());
        static::assertCount(5, $mixed->getElements());
        static::assertSame($mixed->getLineStrings(), $mixed->getElements());
        static::assertSame($empty, $mixed->getLineString(0));
        static::assertSame($line, $mixed->getLineString(1));
        static::assertTrue($mixed->getLineString(2)->isEmpty());
        static::assertSame($otherLine, $mixed->getLineString(3));
        static::assertSame($empty, $mixed->getLineString(-1));
        static::assertSame($empty, $mixed->getLineString(5));
        static::assertSame([$empty], $single->getElements());
        static::assertCount(1, $single->getLineStrings());
        static::assertSame([[]], $single->toArray());
        static::assertSame([], $zero->getElements());
        static::assertCount(0, $zero->getLineStrings());
        static::assertSame([], $zero->toArray());
        static::assertTrue($empty->hasZ());
        static::assertTrue($empty->hasM());
        static::assertSame(4326, $empty->getSrid());
    }

    /** Reference copies preserve empty positions, ordinates and the source graph. */
    public function testGeographyReferenceCopies(): void
    {
        $original = new GeographicMultiLineString([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], 4326);
        $reference = SpatialReference::epsg(2154);
        $referenced = $original->withSpatialReference($reference);
        $renumbered = $original->withSrid(3857);

        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], $referenced->toArray());
        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], $renumbered->toArray());
        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], $original->toArray());
        static::assertCount(3, $referenced->getElements());
        static::assertCount(3, $renumbered->getElements());
        static::assertSame($reference, $referenced->getSpatialReference());
        static::assertSame($reference, $referenced->getLineString(0)->getSpatialReference());
        static::assertSame($reference, $referenced->getLineString(1)->getPoint(0)->getSpatialReference());
        static::assertSame($reference, $referenced->getLineString(2)->getSpatialReference());
        static::assertSame(3857, $renumbered->getLineString(0)->getSrid());
        static::assertSame(3857, $renumbered->getLineString(1)->getPoint(0)->getSrid());
        static::assertSame(3857, $renumbered->getLineString(2)->getSrid());
        static::assertSame(4326, $original->getLineString(0)->getSrid());
        static::assertSame(4326, $original->getLineString(1)->getPoint(0)->getSrid());
        static::assertSame(4326, $original->getLineString(2)->getSrid());
        static::assertNotSame($original->getLineString(0), $referenced->getLineString(0));
        static::assertNotSame($original->getLineString(2), $renumbered->getLineString(2));
        static::assertNotSame($original->getLineString(1)->getPoint(0), $referenced->getLineString(1)->getPoint(0));
        static::assertNotSame($original->getLineString(1)->getPoint(0), $renumbered->getLineString(1)->getPoint(0));
        static::assertInstanceOf(GeographicLineString::class, $referenced->getLineString(0));
        static::assertInstanceOf(GeographicLineString::class, $renumbered->getLineString(2));
        static::assertTrue($referenced->getLineString(0)->hasZ());
        static::assertTrue($renumbered->getLineString(2)->hasM());
    }

    /** Coordinate arrays create empty members in the receiving context. */
    public function testGeometryCoordinateArrays(): void
    {
        $mixed = new MultiLineString([[], [[1, 2, 3, 7], [4, 5, 6, 8]], [], [[7, 8, 9, 10], [11, 12, 13, 14]], []], 4326);
        $single = new MultiLineString([[]], 4326);

        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], [], [[7, 8, 9, 10], [11, 12, 13, 14]], []], $mixed->toArray());
        static::assertCount(5, $mixed->getElements());
        static::assertInstanceOf(LineString::class, $mixed->getLineString(0));
        static::assertSame([], $mixed->getLineString(0)->toArray());
        static::assertSame([], $mixed->getLineString(2)->toArray());
        static::assertSame([], $mixed->getLineString(4)->toArray());
        static::assertTrue($mixed->getLineString(0)->hasZ());
        static::assertTrue($mixed->getLineString(0)->hasM());
        static::assertSame(4326, $mixed->getLineString(0)->getSrid());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
    }

    /** Replacement keeps untouched empty members and can empty or fill a member. */
    public function testGeometryLineStringReplacement(): void
    {
        $original = new MultiLineString([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], 4326);
        $emptied = $original->withLineString(1, []);
        $filled = $original->withLineString(-1, [[1, 2, 3, 7], [4, 5, 6, 8]]);

        static::assertSame([[], [], []], $emptied->toArray());
        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], [[1, 2, 3, 7], [4, 5, 6, 8]]], $filled->toArray());
        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], $original->toArray());
        static::assertCount(3, $emptied->getElements());
        static::assertCount(3, $filled->getElements());
        static::assertNotSame($original, $emptied);
        static::assertNotSame($original->getLineString(0), $emptied->getLineString(0));
        static::assertNotSame($original->getLineString(2), $emptied->getLineString(2));
        static::assertNotSame($original->getLineString(1), $filled->getLineString(1));
        static::assertNotSame($original->getLineString(1)->getPoint(0), $filled->getLineString(1)->getPoint(0));
        static::assertInstanceOf(LineString::class, $emptied->getLineString(1));
        static::assertSame(4326, $emptied->getLineString(1)->getSrid());
        static::assertTrue($emptied->getLineString(1)->hasZ());
        static::assertTrue($emptied->getLineString(1)->hasM());
    }

    /** Object members retain their count, order and accessors. */
    public function testGeometryObjectMembers(): void
    {
        $empty = new LineString([], 4326);
        $line = new LineString([[1, 2, 3, 7], [4, 5, 6, 8]], 4326);
        $otherLine = new LineString([[7, 8, 9, 10], [11, 12, 13, 14]], 4326);
        $mixed = new MultiLineString([$empty, $line, new LineString([], 4326), $otherLine, $empty], 4326);
        $single = new MultiLineString([$empty], 4326);
        $zero = new MultiLineString([], 4326);

        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], [], [[7, 8, 9, 10], [11, 12, 13, 14]], []], $mixed->toArray());
        static::assertCount(5, $mixed->getElements());
        static::assertSame($mixed->getLineStrings(), $mixed->getElements());
        static::assertSame($empty, $mixed->getLineString(0));
        static::assertSame($line, $mixed->getLineString(1));
        static::assertTrue($mixed->getLineString(2)->isEmpty());
        static::assertSame($otherLine, $mixed->getLineString(3));
        static::assertSame($empty, $mixed->getLineString(-1));
        static::assertSame($empty, $mixed->getLineString(5));
        static::assertSame([$empty], $single->getElements());
        static::assertCount(1, $single->getLineStrings());
        static::assertSame([[]], $single->toArray());
        static::assertSame([], $zero->getElements());
        static::assertCount(0, $zero->getLineStrings());
        static::assertSame([], $zero->toArray());
        static::assertTrue($empty->hasZ());
        static::assertTrue($empty->hasM());
        static::assertSame(4326, $empty->getSrid());
    }

    /** Reference copies preserve empty positions, ordinates and the source graph. */
    public function testGeometryReferenceCopies(): void
    {
        $original = new MultiLineString([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], 4326);
        $reference = SpatialReference::epsg(2154);
        $referenced = $original->withSpatialReference($reference);
        $renumbered = $original->withSrid(3857);

        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], $referenced->toArray());
        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], $renumbered->toArray());
        static::assertSame([[], [[1, 2, 3, 7], [4, 5, 6, 8]], []], $original->toArray());
        static::assertCount(3, $referenced->getElements());
        static::assertCount(3, $renumbered->getElements());
        static::assertSame($reference, $referenced->getSpatialReference());
        static::assertSame($reference, $referenced->getLineString(0)->getSpatialReference());
        static::assertSame($reference, $referenced->getLineString(1)->getPoint(0)->getSpatialReference());
        static::assertSame($reference, $referenced->getLineString(2)->getSpatialReference());
        static::assertSame(3857, $renumbered->getLineString(0)->getSrid());
        static::assertSame(3857, $renumbered->getLineString(1)->getPoint(0)->getSrid());
        static::assertSame(3857, $renumbered->getLineString(2)->getSrid());
        static::assertSame(4326, $original->getLineString(0)->getSrid());
        static::assertSame(4326, $original->getLineString(1)->getPoint(0)->getSrid());
        static::assertSame(4326, $original->getLineString(2)->getSrid());
        static::assertNotSame($original->getLineString(0), $referenced->getLineString(0));
        static::assertNotSame($original->getLineString(2), $renumbered->getLineString(2));
        static::assertNotSame($original->getLineString(1)->getPoint(0), $referenced->getLineString(1)->getPoint(0));
        static::assertNotSame($original->getLineString(1)->getPoint(0), $renumbered->getLineString(1)->getPoint(0));
        static::assertInstanceOf(LineString::class, $referenced->getLineString(0));
        static::assertInstanceOf(LineString::class, $renumbered->getLineString(2));
        static::assertTrue($referenced->getLineString(0)->hasZ());
        static::assertTrue($renumbered->getLineString(2)->hasM());
    }
}
