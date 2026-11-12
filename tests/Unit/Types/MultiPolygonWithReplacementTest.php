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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Types;

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygonImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiPolygonImmutable
 */
class MultiPolygonWithReplacementTest extends TestCase
{
    /**
     * Create a valid two-dimensional multi-polygon with an exterior ring and a contained hole.
     */
    private static function createMultiPolygon(): MultiPolygonImmutable
    {
        return new MultiPolygonImmutable([
            [
                [[0, 0], [10, 0], [0, 10], [0, 0]],
                [[1, 1], [2, 1], [1, 2], [1, 1]],
            ],
            [
                [[20, 0], [30, 0], [20, 10], [20, 0]],
            ],
        ], 2154);
    }

    /**
     * Verify that one point is replaced in a deep copy of a multi-polygon.
     */
    public function testWithPointReturnsAnIndependentCopy(): void
    {
        $multiPolygon = self::createMultiPolygon();
        $replacement = $multiPolygon->withPoint(0, 0, 0, Coordinates::xy(-1, -1));

        self::assertNotSame($multiPolygon, $replacement);
        self::assertSame($multiPolygon::class, new \ReflectionClass($replacement)->getName());
        self::assertSame(2154, $replacement->getSrid());
        self::assertSame([[[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $multiPolygon->toArray());
        self::assertSame([[[[-1, -1], [10, 0], [0, 10], [-1, -1]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $replacement->toArray());
        self::assertNotSame($multiPolygon->getPolygon(0), $replacement->getPolygon(0));
        self::assertNotSame($multiPolygon->getPolygon(1), $replacement->getPolygon(1));
    }

    /**
     * Verify that one polygon is replaced in a deep copy of a multi-polygon.
     */
    public function testWithPolygonReturnsAnIndependentCopy(): void
    {
        $multiPolygon = self::createMultiPolygon();
        $replacement = $multiPolygon->withPolygon(0, [
            [[40, 0], [50, 0], [40, 10], [40, 0]],
            [[41, 1], [42, 1], [41, 2], [41, 1]],
        ]);

        self::assertNotSame($multiPolygon, $replacement);
        self::assertSame([[[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $multiPolygon->toArray());
        self::assertSame([[[[40, 0], [50, 0], [40, 10], [40, 0]], [[41, 1], [42, 1], [41, 2], [41, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $replacement->toArray());
        self::assertNotSame($multiPolygon->getPolygon(0), $replacement->getPolygon(0));
        self::assertNotSame($multiPolygon->getPolygon(1), $replacement->getPolygon(1));
    }

    /**
     * Verify that one ring is replaced in a deep copy of a multi-polygon.
     */
    public function testWithRingReturnsAnIndependentCopy(): void
    {
        $multiPolygon = self::createMultiPolygon();
        $replacement = $multiPolygon->withRing(-1, 0, [[19, -1], [31, -1], [19, 11], [19, -1]]);

        self::assertNotSame($multiPolygon, $replacement);
        self::assertSame([[[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $multiPolygon->toArray());
        self::assertSame([[[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[19, -1], [31, -1], [19, 11], [19, -1]]]], $replacement->toArray());
        self::assertNotSame($multiPolygon->getPolygon(0), $replacement->getPolygon(0));
        self::assertNotSame($multiPolygon->getPolygon(1), $replacement->getPolygon(1));
    }
}
