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

use LongitudeOne\SpatialTypes\Types\Dimension2\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CompoundCurveImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\MultiPoint;
use PHPUnit\Framework\TestCase;

/** Numeric equivalence at existing predicate call sites, without a new XY/3D mapping.
 * @internal
 *
 * @coversNothing
 */
final class NumericEqualityPredicateTest extends TestCase
{
    /** Closure observes numeric endpoint equivalence without replacing coordinates. */
    public function testCircularStringImmutableClosure(): void
    {
        $curve = new CircularStringImmutable([[1, 2], [3, 4], [1.0, 2.0]], 4326);

        self::assertSame([[1, 2], [3, 4], [1.0, 2.0]], $curve->toArray());
        self::assertSame(4326, $curve->getSrid());
    }

    /** Closure observes numeric endpoint equivalence without replacing coordinates. */
    public function testCircularStringMutableClosure(): void
    {
        $curve = new CircularString([[1, 2], [3, 4], [1.0, 2.0]], 4326);

        self::assertSame([[1, 2], [3, 4], [1.0, 2.0]], $curve->toArray());
        self::assertSame(4326, $curve->getSrid());
    }

    /** Closure observes numeric endpoint equivalence without replacing coordinates. */
    public function testCompoundCurveImmutableClosure(): void
    {
        $curve = new CompoundCurveImmutable([
            new LineStringImmutable([[1, 2], [3, 4]], 4326),
            new LineStringImmutable([[3, 4], [1.0, 2.0]], 4326),
        ], 4326);

        self::assertSame([[[1, 2], [3, 4]], [[3, 4], [1.0, 2.0]]], $curve->toArray());
        self::assertSame(4326, $curve->getSrid());
    }

    /** Closure observes numeric endpoint equivalence without replacing coordinates. */
    public function testCompoundCurveMutableClosure(): void
    {
        $curve = new CompoundCurve([
            new LineString([[1, 2], [3, 4]], 4326),
            new LineString([[3, 4], [1.0, 2.0]], 4326),
        ], 4326);

        self::assertSame([[[1, 2], [3, 4]], [[3, 4], [1.0, 2.0]]], $curve->toArray());
        self::assertSame(4326, $curve->getSrid());
    }

    /** The existing duplicate-position simplicity path uses numeric equivalence too. */
    public function testImmutableLineStringNumericDuplicateSimplicity(): void
    {
        $curve = new LineStringImmutable([[1, 2], [3, 4], [1.0, 2.0]]);

        self::assertSame([[1, 2], [3, 4], [1.0, 2.0]], $curve->toArray());
    }

    /** Numeric duplicates remain stored and make the multipoint non-simple. */
    public function testImmutableMultiPointSimplicity(): void
    {
        $points = new MultiPointImmutable([[1, 2], [1.0, 2.0]], 4326);

        self::assertSame([[1, 2], [1.0, 2.0]], $points->toArray());
        self::assertSame(4326, $points->getSrid());
    }

    /** Closure observes numeric endpoint equivalence without replacing coordinates. */
    public function testLineStringImmutableClosure(): void
    {
        $curve = new LineStringImmutable([[1, 2], [3, 4], [1.0, 2.0]], 4326);

        self::assertSame([[1, 2], [3, 4], [1.0, 2.0]], $curve->toArray());
        self::assertSame(4326, $curve->getSrid());
    }

    /** Closure observes numeric endpoint equivalence without replacing coordinates. */
    public function testLineStringMutableClosure(): void
    {
        $curve = new LineString([[1, 2], [3, 4], [1.0, 2.0]], 4326);

        self::assertSame([[1, 2], [3, 4], [1.0, 2.0]], $curve->toArray());
        self::assertSame(4326, $curve->getSrid());
    }

    /** Numeric duplicates remain stored and make the multipoint non-simple. */
    public function testMutableMultiPointSimplicity(): void
    {
        $points = new MultiPoint([[1, 2], [1.0, 2.0]], 4326);

        self::assertSame([[1, 2], [1.0, 2.0]], $points->toArray());
        self::assertSame(4326, $points->getSrid());
    }
}
