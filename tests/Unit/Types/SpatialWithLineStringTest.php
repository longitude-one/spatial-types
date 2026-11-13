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

use LongitudeOne\SpatialTypes\Internal\Factory\FromIndexedArrayFactory;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiLineStringImmutable
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractPolygonImmutable
 */
class SpatialWithLineStringTest extends TestCase
{
    /**
     * Verify that a multi-line string replaces one line in a deep immutable copy.
     */
    public function testMultiLineStringWithLineStringReturnsAnIndependentCopy(): void
    {
        $multiLineString = new MultiLineStringImmutable([
            [[0, 0], [1, 1]],
            [[2, 2], [3, 3]],
        ], 2154);
        $replacement = $multiLineString->withLineString(-1, [[4, 4], [5, 5]]);

        self::assertNotSame($multiLineString, $replacement);
        self::assertSame($multiLineString::class, new \ReflectionClass($replacement)->getName());
        self::assertSame(2154, $replacement->getSrid());
        self::assertSame([[[0, 0], [1, 1]], [[2, 2], [3, 3]]], $multiLineString->toArray());
        self::assertSame([[[0, 0], [1, 1]], [[4, 4], [5, 5]]], $replacement->toArray());
        self::assertNotSame($multiLineString->getLineString(0), $replacement->getLineString(0));
        self::assertNotSame($multiLineString->getLineString(1), $replacement->getLineString(1));
    }

    /**
     * Verify that a polygon replaces one ring in a deep immutable copy.
     */
    public function testPolygonWithRingReturnsAnIndependentCopy(): void
    {
        $polygon = FromIndexedArrayFactory::createPolygon([
            [[0, 0], [10, 0], [0, 10], [0, 0]],
            [[1, 1], [2, 1], [1, 2], [1, 1]],
        ], 2154);
        $replacement = $polygon->withRing(0, [[-1, -1], [11, -1], [-1, 11], [-1, -1]]);

        self::assertNotSame($polygon, $replacement);
        self::assertSame($polygon::class, new \ReflectionClass($replacement)->getName());
        self::assertSame(2154, $replacement->getSrid());
        self::assertSame([[[0, 0], [10, 0], [0, 10], [0, 0]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $polygon->toArray());
        self::assertSame([[[-1, -1], [11, -1], [-1, 11], [-1, -1]], [[1, 1], [2, 1], [1, 2], [1, 1]]], $replacement->toArray());
        self::assertNotSame($polygon->getRing(0), $replacement->getRing(0));
        self::assertNotSame($polygon->getRing(1), $replacement->getRing(1));
    }
}
