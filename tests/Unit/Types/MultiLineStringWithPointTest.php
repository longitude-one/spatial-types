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

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiLineStringImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiLineStringImmutable
 */
class MultiLineStringWithPointTest extends TestCase
{
    /**
     * Verify that one point is replaced in a deep copy of a multi-line string.
     */
    public function testWithPointReturnsAnIndependentCopy(): void
    {
        $multiLineString = new MultiLineStringImmutable([
            [[0, 0], [1, 1]],
            [[2, 2], [3, 3]],
        ], 2154);
        $replacement = $multiLineString->withPoint(-1, -1, Coordinates::xy(4, 4));

        self::assertNotSame($multiLineString, $replacement);
        self::assertSame($multiLineString::class, new \ReflectionClass($replacement)->getName());
        self::assertSame(2154, $replacement->getSrid());
        self::assertSame([[[0, 0], [1, 1]], [[2, 2], [3, 3]]], $multiLineString->toArray());
        self::assertSame([[[0, 0], [1, 1]], [[2, 2], [4, 4]]], $replacement->toArray());
        self::assertNotSame($multiLineString->getLineString(0), $replacement->getLineString(0));
        self::assertNotSame($multiLineString->getLineString(1), $replacement->getLineString(1));
    }
}
