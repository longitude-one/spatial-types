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

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Value\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractMultiPointImmutable
 */
class MultiPointWithPointTest extends TestCase
{
    /**
     * Verify that a multi-point rejects a replacement coordinate from another dimension.
     */
    public function testWithPointRejectsAnotherDimension(): void
    {
        $multiPoint = new MultiPointImmutable([[1, 2, 3]], 2154);

        self::expectException(InvalidDimensionException::class);

        $multiPoint->withPoint(0, Coordinates::xy(4, 5));
    }

    /**
     * Verify that a multi-point replaces one point in a deep immutable copy.
     */
    public function testWithPointReturnsAnIndependentCopy(): void
    {
        $multiPoint = new MultiPointImmutable([[1, 2, 3], [4, 5, 6]], 2154);
        $replacement = $multiPoint->withPoint(-1, Coordinates::xym(7, 8, 9));

        self::assertNotSame($multiPoint, $replacement);
        self::assertSame($multiPoint::class, new \ReflectionClass($replacement)->getName());
        self::assertSame(2154, $replacement->getSrid());
        self::assertTrue($replacement->hasM());
        self::assertFalse($replacement->hasZ());
        self::assertSame([[1, 2, 3], [4, 5, 6]], $multiPoint->toArray());
        self::assertSame([[1, 2, 3], [7, 8, 9]], $replacement->toArray());
        self::assertNotSame($multiPoint->getPoint(0), $replacement->getPoint(0));
        self::assertNotSame($multiPoint->getPoint(1), $replacement->getPoint(1));
    }
}
