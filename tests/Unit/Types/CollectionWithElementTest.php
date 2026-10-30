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

        static::assertNotSame($collection, $replacement);
        static::assertSame($collection::class, (new \ReflectionClass($replacement))->getName());
        static::assertSame(2154, $replacement->getSrid());
        static::assertSame([[1, 2], [3, 4]], $collection->toArray());
        static::assertSame([[1, 2], [5, 6]], $replacement->toArray());
        static::assertNotSame($collection->getElements()[0], $replacement->getElements()[0]);
        static::assertNotSame($collection->getElements()[1], $replacement->getElements()[1]);
    }
}
