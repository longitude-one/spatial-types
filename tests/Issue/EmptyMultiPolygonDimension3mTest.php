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

use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\MultiPolygonImmutable as GeographicMultiPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PolygonImmutable as GeographicPolygon;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PolygonImmutable;
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

        self::assertCount(1, $single->getElements());
        self::assertSame([$single->getPolygon(0)], $single->getElements());
        self::assertSame([$single->getPolygon(0)], $single->getPolygons());
        self::assertInstanceOf(GeographicPolygon::class, $single->getPolygon(0));
        self::assertSame([[]], $single->toArray());
        self::assertSame(4326, $single->getPolygon(0)->getSrid());
        self::assertFalse($single->getPolygon(0)->hasZ());
        self::assertTrue($single->getPolygon(0)->hasM());
        self::assertCount(5, $mixed->getElements());
        self::assertSame($mixed->getPolygons(), $mixed->getElements());
        self::assertSame([[], [[[0, 0, 2], [4, 0, 2], [4, 3, 2], [0, 0, 2]]], [], [[[10, 0, 2], [14, 0, 2], [14, 3, 2], [10, 0, 2]]], []], $mixed->toArray());
    }

    /** An empty polygon remains one member, distinct from a zero-member collection. */
    public function testGeographyObjectMember(): void
    {
        $polygon = new GeographicPolygon([], 4326);
        $single = new GeographicMultiPolygon([$polygon], 4326);
        $zero = new GeographicMultiPolygon([], 4326);

        self::assertCount(1, $single->getElements());
        self::assertSame([$polygon], $single->getElements());
        self::assertSame([$polygon], $single->getPolygons());
        self::assertSame($polygon, $single->getPolygon(0));
        self::assertSame([[]], $single->toArray());
        self::assertSame([], $zero->getElements());
        self::assertSame([], $zero->getPolygons());
        self::assertSame([], $zero->toArray());
        self::assertSame(4326, $single->getPolygon(0)->getSrid());
        self::assertFalse($single->getPolygon(0)->hasZ());
        self::assertTrue($single->getPolygon(0)->hasM());
    }

    /** Coordinate arrays preserve empty members before, between and after polygons. */
    public function testGeometryCoordinateArrays(): void
    {
        $single = new MultiPolygonImmutable([[]], 4326);
        $mixed = new MultiPolygonImmutable([[], [[[0, 0, 2], [4, 0, 2], [4, 3, 2], [0, 0, 2]]], [], [[[10, 0, 2], [14, 0, 2], [14, 3, 2], [10, 0, 2]]], []], 4326);

        self::assertCount(1, $single->getElements());
        self::assertSame([$single->getPolygon(0)], $single->getElements());
        self::assertSame([$single->getPolygon(0)], $single->getPolygons());
        self::assertInstanceOf(PolygonImmutable::class, $single->getPolygon(0));
        self::assertSame([[]], $single->toArray());
        self::assertSame(4326, $single->getPolygon(0)->getSrid());
        self::assertFalse($single->getPolygon(0)->hasZ());
        self::assertTrue($single->getPolygon(0)->hasM());
        self::assertCount(5, $mixed->getElements());
        self::assertSame($mixed->getPolygons(), $mixed->getElements());
        self::assertSame([[], [[[0, 0, 2], [4, 0, 2], [4, 3, 2], [0, 0, 2]]], [], [[[10, 0, 2], [14, 0, 2], [14, 3, 2], [10, 0, 2]]], []], $mixed->toArray());
    }

    /** An empty polygon remains one member, distinct from a zero-member collection. */
    public function testGeometryObjectMember(): void
    {
        $polygon = new PolygonImmutable([], 4326);
        $single = new MultiPolygonImmutable([$polygon], 4326);
        $zero = new MultiPolygonImmutable([], 4326);

        self::assertCount(1, $single->getElements());
        self::assertSame([$polygon], $single->getElements());
        self::assertSame([$polygon], $single->getPolygons());
        self::assertSame($polygon, $single->getPolygon(0));
        self::assertSame([[]], $single->toArray());
        self::assertSame([], $zero->getElements());
        self::assertSame([], $zero->getPolygons());
        self::assertSame([], $zero->toArray());
        self::assertSame(4326, $single->getPolygon(0)->getSrid());
        self::assertFalse($single->getPolygon(0)->hasZ());
        self::assertTrue($single->getPolygon(0)->hasM());
    }
}
