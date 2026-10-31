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

use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\MultiPolygonImmutable as GeographicMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PolygonImmutable as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PolygonImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Issue #10: empty polygon members in Dimension4zm.
 *
 * @internal
 *
 * @coversNothing
 */
class EmptyMultiPolygonDimension4zmTest extends TestCase
{
    /** Coordinate arrays preserve empty members before, between and after polygons. */
    public function testGeographyCoordinateArrays(): void
    {
        $single = new GeographicMultiPolygon([[]], 4326);
        $mixed = new GeographicMultiPolygon([[], [[[0, 0, 1, 2], [4, 0, 1, 2], [4, 3, 1, 2], [0, 0, 1, 2]]], [], [[[10, 0, 1, 2], [14, 0, 1, 2], [14, 3, 1, 2], [10, 0, 1, 2]]], []], 4326);

        static::assertCount(1, $single->getElements());
        static::assertSame([$single->getPolygon(0)], $single->getElements());
        static::assertSame([$single->getPolygon(0)], $single->getPolygons());
        static::assertInstanceOf(GeographicPolygon::class, $single->getPolygon(0));
        static::assertSame([[]], $single->toArray());
        static::assertSame(4326, $single->getPolygon(0)->getSrid());
        static::assertTrue($single->getPolygon(0)->hasZ());
        static::assertTrue($single->getPolygon(0)->hasM());
        static::assertCount(5, $mixed->getElements());
        static::assertSame($mixed->getPolygons(), $mixed->getElements());
        static::assertSame([[], [[[0, 0, 1, 2], [4, 0, 1, 2], [4, 3, 1, 2], [0, 0, 1, 2]]], [], [[[10, 0, 1, 2], [14, 0, 1, 2], [14, 3, 1, 2], [10, 0, 1, 2]]], []], $mixed->toArray());
    }

    /** An empty polygon remains one member, distinct from a zero-member collection. */
    public function testGeographyObjectMember(): void
    {
        $polygon = new GeographicPolygon([], 4326);
        $single = new GeographicMultiPolygon([$polygon], 4326);
        $zero = new GeographicMultiPolygon([], 4326);

        static::assertCount(1, $single->getElements());
        static::assertSame([$polygon], $single->getElements());
        static::assertSame([$polygon], $single->getPolygons());
        static::assertSame($polygon, $single->getPolygon(0));
        static::assertSame([[]], $single->toArray());
        static::assertSame([], $zero->getElements());
        static::assertSame([], $zero->getPolygons());
        static::assertSame([], $zero->toArray());
        static::assertSame(4326, $single->getPolygon(0)->getSrid());
        static::assertTrue($single->getPolygon(0)->hasZ());
        static::assertTrue($single->getPolygon(0)->hasM());
    }

    /** Coordinate arrays preserve empty members before, between and after polygons. */
    public function testGeometryCoordinateArrays(): void
    {
        $single = new MultiPolygonImmutable([[]], 4326);
        $mixed = new MultiPolygonImmutable([[], [[[0, 0, 1, 2], [4, 0, 1, 2], [4, 3, 1, 2], [0, 0, 1, 2]]], [], [[[10, 0, 1, 2], [14, 0, 1, 2], [14, 3, 1, 2], [10, 0, 1, 2]]], []], 4326);

        static::assertCount(1, $single->getElements());
        static::assertSame([$single->getPolygon(0)], $single->getElements());
        static::assertSame([$single->getPolygon(0)], $single->getPolygons());
        static::assertInstanceOf(PolygonImmutable::class, $single->getPolygon(0));
        static::assertSame([[]], $single->toArray());
        static::assertSame(4326, $single->getPolygon(0)->getSrid());
        static::assertTrue($single->getPolygon(0)->hasZ());
        static::assertTrue($single->getPolygon(0)->hasM());
        static::assertCount(5, $mixed->getElements());
        static::assertSame($mixed->getPolygons(), $mixed->getElements());
        static::assertSame([[], [[[0, 0, 1, 2], [4, 0, 1, 2], [4, 3, 1, 2], [0, 0, 1, 2]]], [], [[[10, 0, 1, 2], [14, 0, 1, 2], [14, 3, 1, 2], [10, 0, 1, 2]]], []], $mixed->toArray());
    }

    /** An empty polygon remains one member, distinct from a zero-member collection. */
    public function testGeometryObjectMember(): void
    {
        $polygon = new PolygonImmutable([], 4326);
        $single = new MultiPolygonImmutable([$polygon], 4326);
        $zero = new MultiPolygonImmutable([], 4326);

        static::assertCount(1, $single->getElements());
        static::assertSame([$polygon], $single->getElements());
        static::assertSame([$polygon], $single->getPolygons());
        static::assertSame($polygon, $single->getPolygon(0));
        static::assertSame([[]], $single->toArray());
        static::assertSame([], $zero->getElements());
        static::assertSame([], $zero->getPolygons());
        static::assertSame([], $zero->toArray());
        static::assertSame(4326, $single->getPolygon(0)->getSrid());
        static::assertTrue($single->getPolygon(0)->hasZ());
        static::assertTrue($single->getPolygon(0)->hasM());
    }
}
