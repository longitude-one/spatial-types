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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Reference;

use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractSpatialTypeImmutable
 * @covers \LongitudeOne\SpatialTypes\Reference\SpatialReference
 *
 * @internal
 */
class SpatialReferenceTest extends TestCase
{
    /** Verify that a typed EPSG reference propagates to aggregate descendants. */
    public function testEpsgReferenceIsPreservedByAnAggregateAndItsChildren(): void
    {
        $reference = SpatialReference::epsg(4326);
        $lineString = new LineStringImmutable([
            new PointImmutable(1, 2, $reference),
            new PointImmutable(3, 4, $reference),
        ], $reference);

        self::assertSame($reference, $lineString->getSpatialReference());
        self::assertSame($reference, $lineString->getPoint(0)->getSpatialReference());
    }

    /** Verify that relabelling a reference copies all aggregate descendants. */
    public function testWithSpatialReferenceDeeplyRelabelsAnAggregate(): void
    {
        $source = SpatialReference::epsg(4326);
        $target = SpatialReference::epsg(2154);
        $lineString = new LineStringImmutable([
            new PointImmutable(1, 2, $source),
            new PointImmutable(3, 4, $source),
        ], $source);

        $copy = $lineString->withSpatialReference($target);

        self::assertSame($target, $copy->getSpatialReference());
        self::assertSame($target, $copy->getPoint(0)->getSpatialReference());
        self::assertSame($source, $lineString->getSpatialReference());
    }
}
