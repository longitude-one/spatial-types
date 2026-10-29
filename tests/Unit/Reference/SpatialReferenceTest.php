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

namespace LongitudeOne\SpatialTypes\Tests\Unit\Reference;

use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LongitudeOne\SpatialTypes\Reference\SpatialReference
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractSpatialTypeImmutable
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

        static::assertSame($reference, $lineString->getSpatialReference());
        static::assertSame($reference, $lineString->getPoint(0)->getSpatialReference());
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

        static::assertSame($target, $copy->getSpatialReference());
        static::assertSame($target, $copy->getPoint(0)->getSpatialReference());
        static::assertSame($source, $lineString->getSpatialReference());
    }
}
