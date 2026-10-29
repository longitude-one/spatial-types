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

namespace LongitudeOne\SpatialTypes\Tests\Issue\Issue1;

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Regression tests for issue #1: nested spatial collections.
 *
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Types\AbstractCollectionImmutable
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

        static::assertSame([$nestedCollection], $collection->getElements());
        static::assertSame([$point], $nestedCollection->getElements());
        static::assertSame([[[1, 2]]], $collection->toArray());
    }
}
