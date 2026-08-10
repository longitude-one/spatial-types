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

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPolygon;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiPolygon
 */
class MultiPolygonWithReplacementTest extends TestCase
{
    /**
     * Create a valid two-dimensional multi-polygon with an exterior ring and a contained hole.
     */
    private static function createMultiPolygon(): MultiPolygon
    {
        return new MultiPolygon([
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

        static::assertNotSame($multiPolygon, $replacement);
        static::assertSame($multiPolygon::class, $replacement::class);
        static::assertSame(2154, $replacement->getSrid());
        static::assertSame([[[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $multiPolygon->toArray());
        static::assertSame([[[[-1, -1], [10, 0], [0, 10], [-1, -1]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $replacement->toArray());
        static::assertNotSame($multiPolygon->getPolygon(0), $replacement->getPolygon(0));
        static::assertNotSame($multiPolygon->getPolygon(1), $replacement->getPolygon(1));
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

        static::assertNotSame($multiPolygon, $replacement);
        static::assertSame([[[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $multiPolygon->toArray());
        static::assertSame([[[[40, 0], [50, 0], [40, 10], [40, 0]], [[41, 1], [42, 1], [41, 2], [41, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $replacement->toArray());
        static::assertNotSame($multiPolygon->getPolygon(0), $replacement->getPolygon(0));
        static::assertNotSame($multiPolygon->getPolygon(1), $replacement->getPolygon(1));
    }

    /**
     * Verify that one ring is replaced in a deep copy of a multi-polygon.
     */
    public function testWithRingReturnsAnIndependentCopy(): void
    {
        $multiPolygon = self::createMultiPolygon();
        $replacement = $multiPolygon->withRing(-1, 0, [[19, -1], [31, -1], [19, 11], [19, -1]]);

        static::assertNotSame($multiPolygon, $replacement);
        static::assertSame([[[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[20, 0], [30, 0], [20, 10], [20, 0]]]], $multiPolygon->toArray());
        static::assertSame([[[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], [[[19, -1], [31, -1], [19, 11], [19, -1]]]], $replacement->toArray());
        static::assertNotSame($multiPolygon->getPolygon(0), $replacement->getPolygon(0));
        static::assertNotSame($multiPolygon->getPolygon(1), $replacement->getPolygon(1));
    }
}
