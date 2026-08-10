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

use LongitudeOne\SpatialTypes\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineString;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractMultiLineString
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractPolygon
 */
class SpatialWithLineStringTest extends TestCase
{
    /**
     * Verify that a multi-line string replaces one line in a deep immutable copy.
     */
    public function testMultiLineStringWithLineStringReturnsAnIndependentCopy(): void
    {
        $multiLineString = new MultiLineString([
            [[0, 0], [1, 1]],
            [[2, 2], [3, 3]],
        ], 2154);
        $replacement = $multiLineString->withLineString(-1, [[4, 4], [5, 5]]);

        static::assertNotSame($multiLineString, $replacement);
        static::assertSame($multiLineString::class, $replacement::class);
        static::assertSame(2154, $replacement->getSrid());
        static::assertSame([[[0, 0], [1, 1]], [[2, 2], [3, 3]]], $multiLineString->toArray());
        static::assertSame([[[0, 0], [1, 1]], [[4, 4], [5, 5]]], $replacement->toArray());
        static::assertNotSame($multiLineString->getLineString(0), $replacement->getLineString(0));
        static::assertNotSame($multiLineString->getLineString(1), $replacement->getLineString(1));
    }

    /**
     * Verify that a polygon replaces one ring in a deep immutable copy.
     */
    public function testPolygonWithLineStringReturnsAnIndependentCopy(): void
    {
        $polygon = FromIndexedArrayFactory::createPolygon([
            [[0, 0], [10, 0], [0, 10], [0, 0]],
            [[1, 1], [2, 1], [1, 2], [1, 1]],
        ], 2154);
        $replacement = $polygon->withLineString(0, [[-1, -1], [11, -1], [-1, 11], [-1, -1]]);

        static::assertNotSame($polygon, $replacement);
        static::assertSame($polygon::class, $replacement::class);
        static::assertSame(2154, $replacement->getSrid());
        static::assertSame([[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $polygon->toArray());
        static::assertSame([[[-1, -1], [11, -1], [-1, 11], [-1, -1]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $replacement->toArray());
        static::assertNotSame($polygon->getRing(0), $replacement->getRing(0));
        static::assertNotSame($polygon->getRing(1), $replacement->getRing(1));
    }
}
