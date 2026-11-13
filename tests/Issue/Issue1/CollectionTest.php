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

namespace LongitudeOne\SpatialTypes\Tests\Issue\Issue1;

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Regression tests for issue #1: nested spatial collections.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractCollectionImmutable
 * @covers \LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable
 */
class CollectionTest extends TestCase
{
    /**
     * Test that a collection can contain another non-empty collection.
     *
     * @see https://github.com/longitude-one/spatial-types/issues/1
     */
    public function testCollectionCanContainAnotherCollection(): void
    {
        $point = new PointImmutable(1, 2, 4326);
        $nestedCollection = new GeometryCollectionImmutable(4326, [$point]);
        $collection = new GeometryCollectionImmutable(4326, [$nestedCollection]);

        self::assertSame([$nestedCollection], $collection->getElements());
        self::assertSame([$point], $nestedCollection->getElements());
        self::assertSame([[[1, 2]]], $collection->toArray());
    }
}
