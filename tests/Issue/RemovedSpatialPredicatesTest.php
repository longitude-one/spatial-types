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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\SpatialTypes\Interfaces\CurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MultiPointInterface;
use LongitudeOne\SpatialTypes\Interfaces\SpatialInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable;
use PHPUnit\Framework\TestCase;

/**
 * Story #71 regression coverage for the representation-only public API.
 *
 * @internal
 *
 * @coversNothing
 */
class RemovedSpatialPredicatesTest extends TestCase
{
    /** Closed coordinate sequences remain representable without evaluating closure. */
    public function testClosedRepresentationDoesNotExposePredicate(): void
    {
        $immutable = new LineStringImmutable([[0, 0], [2, 2], [0, 0]]);
        $mutable = new LineString([[0, 0], [2, 2], [0, 0]]);

        static::assertSame([[0, 0], [2, 2], [0, 0]], $immutable->toArray());
        static::assertSame([[0, 0], [2, 2], [0, 0]], $mutable->toArray());
        $method = sprintf('is%s', 'Closed');
        static::assertFalse(method_exists(CurveInterface::class, $method));
        static::assertNotContains($method, get_class_methods($immutable));
        static::assertNotContains($method, get_class_methods($mutable));
    }

    /** Emptiness remains representable without exposing an emptiness predicate. */
    public function testEmptyRepresentationDoesNotExposePredicate(): void
    {
        $immutable = new PointImmutable();
        $mutable = new Point();

        static::assertSame([], $immutable->toArray());
        static::assertSame([], $mutable->toArray());
        $method = sprintf('is%s', 'Empty');
        static::assertFalse(method_exists(SpatialInterface::class, $method));
        static::assertNotContains($method, get_class_methods($immutable));
        static::assertNotContains($method, get_class_methods($mutable));
    }

    /** MultiPoint membership remains observable without evaluating simplicity. */
    public function testMultiPointRepresentationDoesNotExposePredicate(): void
    {
        $immutable = new MultiPointImmutable([[1, 2], [1, 2]]);
        $mutable = new MultiPoint([[1, 2], [1, 2]]);

        static::assertSame([[1, 2], [1, 2]], $immutable->toArray());
        static::assertSame([[1, 2], [1, 2]], $mutable->toArray());
        $method = sprintf('is%s', 'Simple');
        static::assertFalse(method_exists(MultiPointInterface::class, $method));
        static::assertNotContains($method, get_class_methods($immutable));
        static::assertNotContains($method, get_class_methods($mutable));
    }
}
