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

use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygon as GeographicMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\Polygon as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Issue #10: empty polygon members in Dimension3m.
 *
 * @internal
 *
 * @coversNothing
 */
class EmptyMultiPolygonDimension3mTest extends TestCase
{
    /** Coordinate arrays preserve empty members before, between and after polygons. */
    public function testGeographyCoordinateArrays(): void
    {
        $single = new GeographicMultiPolygon([[]], 4326);
        $mixed = new GeographicMultiPolygon([[], [[[0, 0, 2], [4, 0, 2], [4, 3, 2], [0, 0, 2]]], [], [[[10, 0, 2], [14, 0, 2], [14, 3, 2], [10, 0, 2]]], []], 4326);

        static::assertCount(1, $single->getElements());
        static::assertSame([$single->getPolygon(0)], $single->getElements());
        static::assertSame([$single->getPolygon(0)], $single->getPolygons());
        static::assertInstanceOf(GeographicPolygon::class, $single->getPolygon(0));
        static::assertTrue($single->getPolygon(0)->isEmpty());
        static::assertFalse($single->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertSame(4326, $single->getPolygon(0)->getSrid());
        static::assertFalse($single->getPolygon(0)->hasZ());
        static::assertTrue($single->getPolygon(0)->hasM());
        static::assertCount(5, $mixed->getElements());
        static::assertSame($mixed->getPolygons(), $mixed->getElements());
        static::assertTrue($mixed->getPolygon(0)->isEmpty());
        static::assertTrue($mixed->getPolygon(2)->isEmpty());
        static::assertTrue($mixed->getPolygon(4)->isEmpty());
        static::assertSame([[], [[[0, 0, 2], [4, 0, 2], [4, 3, 2], [0, 0, 2]]], [], [[[10, 0, 2], [14, 0, 2], [14, 3, 2], [10, 0, 2]]], []], $mixed->toArray());
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
        static::assertTrue($single->getPolygon(0)->isEmpty());
        static::assertFalse($single->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertSame([], $zero->getElements());
        static::assertSame([], $zero->getPolygons());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->toArray());
        static::assertSame(4326, $single->getPolygon(0)->getSrid());
        static::assertFalse($single->getPolygon(0)->hasZ());
        static::assertTrue($single->getPolygon(0)->hasM());
    }

    /** Coordinate arrays preserve empty members before, between and after polygons. */
    public function testGeometryCoordinateArrays(): void
    {
        $single = new MultiPolygon([[]], 4326);
        $mixed = new MultiPolygon([[], [[[0, 0, 2], [4, 0, 2], [4, 3, 2], [0, 0, 2]]], [], [[[10, 0, 2], [14, 0, 2], [14, 3, 2], [10, 0, 2]]], []], 4326);

        static::assertCount(1, $single->getElements());
        static::assertSame([$single->getPolygon(0)], $single->getElements());
        static::assertSame([$single->getPolygon(0)], $single->getPolygons());
        static::assertInstanceOf(Polygon::class, $single->getPolygon(0));
        static::assertTrue($single->getPolygon(0)->isEmpty());
        static::assertFalse($single->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertSame(4326, $single->getPolygon(0)->getSrid());
        static::assertFalse($single->getPolygon(0)->hasZ());
        static::assertTrue($single->getPolygon(0)->hasM());
        static::assertCount(5, $mixed->getElements());
        static::assertSame($mixed->getPolygons(), $mixed->getElements());
        static::assertTrue($mixed->getPolygon(0)->isEmpty());
        static::assertTrue($mixed->getPolygon(2)->isEmpty());
        static::assertTrue($mixed->getPolygon(4)->isEmpty());
        static::assertSame([[], [[[0, 0, 2], [4, 0, 2], [4, 3, 2], [0, 0, 2]]], [], [[[10, 0, 2], [14, 0, 2], [14, 3, 2], [10, 0, 2]]], []], $mixed->toArray());
    }

    /** An empty polygon remains one member, distinct from a zero-member collection. */
    public function testGeometryObjectMember(): void
    {
        $polygon = new Polygon([], 4326);
        $single = new MultiPolygon([$polygon], 4326);
        $zero = new MultiPolygon([], 4326);

        static::assertCount(1, $single->getElements());
        static::assertSame([$polygon], $single->getElements());
        static::assertSame([$polygon], $single->getPolygons());
        static::assertSame($polygon, $single->getPolygon(0));
        static::assertTrue($single->getPolygon(0)->isEmpty());
        static::assertFalse($single->isEmpty());
        static::assertSame([[]], $single->toArray());
        static::assertSame([], $zero->getElements());
        static::assertSame([], $zero->getPolygons());
        static::assertTrue($zero->isEmpty());
        static::assertSame([], $zero->toArray());
        static::assertSame(4326, $single->getPolygon(0)->getSrid());
        static::assertFalse($single->getPolygon(0)->hasZ());
        static::assertTrue($single->getPolygon(0)->hasM());
    }
}
