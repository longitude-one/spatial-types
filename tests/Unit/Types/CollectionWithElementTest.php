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

use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\GeometryCollectionImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \LongitudeOne\SpatialTypes\Internal\Types\AbstractCollectionImmutable
 */
class CollectionWithElementTest extends TestCase
{
    /**
     * Verify that one element is replaced in a deep copy of a spatial collection.
     */
    public function testWithElementReturnsAnIndependentCopy(): void
    {
        $firstPoint = new PointImmutable(1, 2, 2154);
        $secondPoint = new PointImmutable(3, 4, 2154);
        $collection = new GeometryCollectionImmutable(2154, [$firstPoint, $secondPoint]);

        $replacement = $collection->withElement(-1, new PointImmutable(5, 6, 2154));

        self::assertNotSame($collection, $replacement);
        self::assertSame($collection::class, new \ReflectionClass($replacement)->getName());
        self::assertSame(2154, $replacement->getSrid());
        self::assertSame([[1, 2], [3, 4]], $collection->toArray());
        self::assertSame([[1, 2], [5, 6]], $replacement->toArray());
        self::assertNotSame($collection->getElements()[0], $replacement->getElements()[0]);
        self::assertNotSame($collection->getElements()[1], $replacement->getElements()[1]);
    }
}
