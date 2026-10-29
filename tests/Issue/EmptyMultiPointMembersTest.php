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
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for issue #8: preserve empty MultiPoint members.
 *
 * @internal
 *
 * @coversNothing
 */
class EmptyMultiPointMembersTest extends TestCase
{
    /** Preserve empty members in Dimension2 Geography. */
    public function testDimension2GeographyPreservesEmptyMembers(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(srid: 4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(1, 2, srid: 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable([$empty, $point, new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(srid: 4326), $point, $empty], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable([$empty], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\MultiPointImmutable([], 4326);

        static::assertSame([[], [1, 2], [], [1, 2], []], $multiPoint->toArray());
        static::assertCount(5, $multiPoint->getElements());
        static::assertSame($empty, $multiPoint->getPoint(0));
        static::assertSame($empty, $multiPoint->getPoint(-1));
        static::assertTrue($multiPoint->getPoint(2)->isEmpty());
        static::assertSame($multiPoint->getPoints(), $multiPoint->getElements());
        static::assertFalse($multiPoint->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
        static::assertFalse($single->isEmpty());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->getElements());
        static::assertTrue($empty->equalsTo($multiPoint->getPoint(2)));
        static::assertFalse($empty->equalsTo($point));
    }

    /** Preserve empty members in Dimension2 Geometry. */
    public function testDimension2GeometryPreservesEmptyMembers(): void
    {
        $empty = new PointImmutable(srid: 4326);
        $point = new PointImmutable(1, 2, srid: 4326);
        $multiPoint = new MultiPointImmutable([$empty, $point, new PointImmutable(srid: 4326), $point, $empty], 4326);
        $single = new MultiPointImmutable([$empty], 4326);
        $zero = new MultiPointImmutable([], 4326);

        static::assertSame([[], [1, 2], [], [1, 2], []], $multiPoint->toArray());
        static::assertCount(5, $multiPoint->getElements());
        static::assertSame($empty, $multiPoint->getPoint(0));
        static::assertSame($empty, $multiPoint->getPoint(-1));
        static::assertTrue($multiPoint->getPoint(2)->isEmpty());
        static::assertSame($multiPoint->getPoints(), $multiPoint->getElements());
        static::assertFalse($multiPoint->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
        static::assertFalse($single->isEmpty());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->getElements());
        static::assertTrue($empty->equalsTo($multiPoint->getPoint(2)));
        static::assertFalse($empty->equalsTo($point));
    }

    /** Preserve empty members in Dimension3m Geography. */
    public function testDimension3mGeographyPreservesEmptyMembers(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(srid: 4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(1, 2, 4, srid: 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([$empty, $point, new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(srid: 4326), $point, $empty], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([$empty], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPointImmutable([], 4326);

        static::assertSame([[], [1, 2, 4], [], [1, 2, 4], []], $multiPoint->toArray());
        static::assertCount(5, $multiPoint->getElements());
        static::assertSame($empty, $multiPoint->getPoint(0));
        static::assertSame($empty, $multiPoint->getPoint(-1));
        static::assertTrue($multiPoint->getPoint(2)->isEmpty());
        static::assertSame($multiPoint->getPoints(), $multiPoint->getElements());
        static::assertFalse($multiPoint->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
        static::assertFalse($single->isEmpty());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->getElements());
        static::assertTrue($empty->equalsTo($multiPoint->getPoint(2)));
        static::assertFalse($empty->equalsTo($point));
    }

    /** Preserve empty members in Dimension3m Geometry. */
    public function testDimension3mGeometryPreservesEmptyMembers(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(srid: 4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1, 2, 4, srid: 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$empty, $point, new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(srid: 4326), $point, $empty], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([$empty], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable([], 4326);

        static::assertSame([[], [1, 2, 4], [], [1, 2, 4], []], $multiPoint->toArray());
        static::assertCount(5, $multiPoint->getElements());
        static::assertSame($empty, $multiPoint->getPoint(0));
        static::assertSame($empty, $multiPoint->getPoint(-1));
        static::assertTrue($multiPoint->getPoint(2)->isEmpty());
        static::assertSame($multiPoint->getPoints(), $multiPoint->getElements());
        static::assertFalse($multiPoint->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
        static::assertFalse($single->isEmpty());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->getElements());
        static::assertTrue($empty->equalsTo($multiPoint->getPoint(2)));
        static::assertFalse($empty->equalsTo($point));
    }

    /** Preserve empty members in Dimension3z Geography. */
    public function testDimension3zGeographyPreservesEmptyMembers(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(srid: 4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(1, 2, 3, srid: 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([$empty, $point, new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(srid: 4326), $point, $empty], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([$empty], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\MultiPointImmutable([], 4326);

        static::assertSame([[], [1, 2, 3], [], [1, 2, 3], []], $multiPoint->toArray());
        static::assertCount(5, $multiPoint->getElements());
        static::assertSame($empty, $multiPoint->getPoint(0));
        static::assertSame($empty, $multiPoint->getPoint(-1));
        static::assertTrue($multiPoint->getPoint(2)->isEmpty());
        static::assertSame($multiPoint->getPoints(), $multiPoint->getElements());
        static::assertFalse($multiPoint->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
        static::assertFalse($single->isEmpty());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->getElements());
        static::assertTrue($empty->equalsTo($multiPoint->getPoint(2)));
        static::assertFalse($empty->equalsTo($point));
    }

    /** Preserve empty members in Dimension3z Geometry. */
    public function testDimension3zGeometryPreservesEmptyMembers(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(srid: 4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1, 2, 3, srid: 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([$empty, $point, new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(srid: 4326), $point, $empty], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([$empty], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\MultiPointImmutable([], 4326);

        static::assertSame([[], [1, 2, 3], [], [1, 2, 3], []], $multiPoint->toArray());
        static::assertCount(5, $multiPoint->getElements());
        static::assertSame($empty, $multiPoint->getPoint(0));
        static::assertSame($empty, $multiPoint->getPoint(-1));
        static::assertTrue($multiPoint->getPoint(2)->isEmpty());
        static::assertSame($multiPoint->getPoints(), $multiPoint->getElements());
        static::assertFalse($multiPoint->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
        static::assertFalse($single->isEmpty());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->getElements());
        static::assertTrue($empty->equalsTo($multiPoint->getPoint(2)));
        static::assertFalse($empty->equalsTo($point));
    }

    /** Preserve empty members in Dimension4zm Geography. */
    public function testDimension4zmGeographyPreservesEmptyMembers(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(srid: 4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(1, 2, 3, 4, srid: 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([$empty, $point, new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(srid: 4326), $point, $empty], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([$empty], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPointImmutable([], 4326);

        static::assertSame([[], [1, 2, 3, 4], [], [1, 2, 3, 4], []], $multiPoint->toArray());
        static::assertCount(5, $multiPoint->getElements());
        static::assertSame($empty, $multiPoint->getPoint(0));
        static::assertSame($empty, $multiPoint->getPoint(-1));
        static::assertTrue($multiPoint->getPoint(2)->isEmpty());
        static::assertSame($multiPoint->getPoints(), $multiPoint->getElements());
        static::assertFalse($multiPoint->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
        static::assertFalse($single->isEmpty());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->getElements());
        static::assertTrue($empty->equalsTo($multiPoint->getPoint(2)));
        static::assertFalse($empty->equalsTo($point));
    }

    /** Preserve empty members in Dimension4zm Geometry. */
    public function testDimension4zmGeometryPreservesEmptyMembers(): void
    {
        $empty = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(srid: 4326);
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1, 2, 3, 4, srid: 4326);
        $multiPoint = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([$empty, $point, new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(srid: 4326), $point, $empty], 4326);
        $single = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([$empty], 4326);
        $zero = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPointImmutable([], 4326);

        static::assertSame([[], [1, 2, 3, 4], [], [1, 2, 3, 4], []], $multiPoint->toArray());
        static::assertCount(5, $multiPoint->getElements());
        static::assertSame($empty, $multiPoint->getPoint(0));
        static::assertSame($empty, $multiPoint->getPoint(-1));
        static::assertTrue($multiPoint->getPoint(2)->isEmpty());
        static::assertSame($multiPoint->getPoints(), $multiPoint->getElements());
        static::assertFalse($multiPoint->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertCount(1, $single->getElements());
        static::assertFalse($single->isEmpty());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->getElements());
        static::assertTrue($empty->equalsTo($multiPoint->getPoint(2)));
        static::assertFalse($empty->equalsTo($point));
    }

    /** Immutable copies preserve empty members and the original. */
    public function testImmutableCopiesPreserveEmptyMembers(): void
    {
        $original = new MultiPointImmutable([new PointImmutable(srid: 4326), new PointImmutable(1, 2, 4326), new PointImmutable(srid: 4326)], 4326);
        $reference = SpatialReference::epsg(2154);
        $declared = $original->withSpatialReference($reference);
        $renumbered = $original->withSrid(2154);
        $replaced = $original->withPoint(0, Coordinates::xy(3, 4));

        static::assertSame([[], [1, 2], []], $original->toArray());
        static::assertSame([[], [1, 2], []], $declared->toArray());
        static::assertSame([[], [1, 2], []], $renumbered->toArray());
        static::assertSame([[3, 4], [1, 2], []], $replaced->toArray());
        static::assertSame(4326, $original->getPoint(0)->getSrid());
        static::assertSame(2154, $renumbered->getPoint(0)->getSrid());
        static::assertSame(2154, $renumbered->getPoint(2)->getSrid());
        static::assertSame($reference, $declared->getPoint(0)->getSpatialReference());
        static::assertSame($reference, $declared->getPoint(2)->getSpatialReference());
        static::assertNotSame($original->getPoint(2), $replaced->getPoint(2));
        static::assertNotSame($original->getPoint(0), $declared->getPoint(0));
    }

    /** The library JSON export retains member positions. */
    public function testJsonSerializationPreservesEmptyMembers(): void
    {
        $multiPoint = new MultiPointImmutable([new PointImmutable(srid: 4326), new PointImmutable(1, 2, 4326), new PointImmutable(srid: 4326)], 4326);

        static::assertSame('{"type":"MultiPoint","coordinates":[[],[1,2],[]],"srid":4326}', json_encode($multiPoint));
        static::assertSame('{"type":"MultiPoint","coordinates":[[]],"srid":0}', json_encode(new MultiPointImmutable([new PointImmutable()])));
        static::assertSame('{"type":"MultiPoint","coordinates":[],"srid":0}', json_encode(new MultiPointImmutable([])));
    }
}
