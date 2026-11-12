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

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\MultiPointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension4zm\Point;
use PHPUnit\Framework\TestCase;

/** Exact mixed numeric comparisons at binary64 and integer-range boundaries.
 * @internal
 *
 * @coversNothing
 */
final class LargeNumericPointEqualityTest extends TestCase
{
    /** Reproduce the reviewed curve and retain distinct endpoints and their types. */
    public function testDistinctLargeEndpointsDoNotCloseCurve(): void
    {
        $curve = new LineStringImmutable([[9007199254740993, 2], [0, 0], [9007199254740992.0, 2]]);

        static::assertSame([[9007199254740993, 2], [0, 0], [9007199254740992.0, 2]], $curve->toArray());
        $start = $curve->getStartPoint();
        $end = $curve->getEndPoint();
        static::assertNotNull($start);
        static::assertNotNull($end);
        static::assertSame(9007199254740993, $start->getX());
        static::assertSame(9007199254740992.0, $end->getX());
    }

    /** Reproduce the reviewed multipoint without merging its two member positions. */
    public function testDistinctLargeMembersRemainSimple(): void
    {
        $integer = new PointImmutable(9007199254740993, 2);
        $floating = new PointImmutable(9007199254740992.0, 2);
        $points = new MultiPointImmutable([$integer, $floating]);

        static::assertSame($integer, $points->getPoint(0));
        static::assertSame($floating, $points->getPoint(1));
        static::assertSame([[9007199254740993, 2], [9007199254740992.0, 2]], $points->toArray());
        static::assertSame([9007199254740993, 2], $integer->toArray());
        static::assertSame([9007199254740992.0, 2], $floating->toArray());
    }

    /** Negative values must not be merged by the same binary64 rounding. */
    public function testDistinctNegativeValuesBeyondBinary64ExactIntegerRange(): void
    {
        $integer = new PointImmutable(-9007199254740993, 2);
        $floating = new PointImmutable(-9007199254740992.0, 2);

        static::assertFalse($integer->equalsTo($floating));
        static::assertFalse($floating->equalsTo($integer));
        static::assertSame([-9007199254740993, 2], $integer->toArray());
        static::assertSame([-9007199254740992.0, 2], $floating->toArray());
    }

    /** Reproduce the exact distinct representations identified in review. */
    public function testDistinctValuesAboveBinary64ExactIntegerRange(): void
    {
        $integer = new PointImmutable(9007199254740993, 2);
        $floating = new PointImmutable(9007199254740992.0, 2);

        static::assertFalse($integer->equalsTo($floating));
        static::assertFalse($floating->equalsTo($integer));
        static::assertSame([9007199254740993, 2], $integer->toArray());
        static::assertSame([9007199254740992.0, 2], $floating->toArray());
    }

    /** Exact mixed representations at and above 2^53 remain equal. */
    public function testExactlyRepresentableLargeValues(): void
    {
        $boundaryInteger = new PointImmutable(9007199254740992, 2);
        $boundaryFloating = new PointImmutable(9007199254740992.0, 2.0);
        $aboveInteger = new PointImmutable(9007199254740994, 2);
        $aboveFloating = new PointImmutable(9007199254740994.0, 2.0);
        $negativeInteger = new PointImmutable(-9007199254740994, 2);
        $negativeFloating = new PointImmutable(-9007199254740994.0, 2.0);

        static::assertTrue($boundaryInteger->equalsTo($boundaryFloating));
        static::assertTrue($boundaryFloating->equalsTo($boundaryInteger));
        static::assertTrue($aboveInteger->equalsTo($aboveFloating));
        static::assertTrue($aboveFloating->equalsTo($aboveInteger));
        static::assertTrue($negativeInteger->equalsTo($negativeFloating));
        static::assertTrue($negativeFloating->equalsTo($negativeInteger));
        static::assertSame([9007199254740994, 2], $aboveInteger->toArray());
        static::assertSame([9007199254740994.0, 2.0], $aboveFloating->toArray());
    }

    /** A fractional floating ordinate must not be truncated into equality. */
    public function testFractionalValuesAndSignedZero(): void
    {
        $integer = new PointImmutable(1, 2);
        $fractional = new PointImmutable(1.5, 2.0);
        $zero = new PointImmutable(0, 2);
        $negativeZero = new PointImmutable(-0.0, 2.0);

        static::assertFalse($integer->equalsTo($fractional));
        static::assertFalse($fractional->equalsTo($integer));
        static::assertTrue($zero->equalsTo($negativeZero));
        static::assertTrue($negativeZero->equalsTo($zero));
        static::assertSame([1.5, 2.0], $fractional->toArray());
    }

    /** All represented axes share the exact mixed comparison. */
    public function testLargeDistinctMutableGeographyOrdinates(): void
    {
        $integer = new Point(1, 9007199254740993, 9007199254740993, 9007199254740993, 4326, SpatialModelEnum::GEOGRAPHY);
        $y = new Point(1.0, 9007199254740992.0, 9007199254740993, 9007199254740993, 4326, SpatialModelEnum::GEOGRAPHY);
        $z = new Point(1.0, 9007199254740993, 9007199254740992.0, 9007199254740993, 4326, SpatialModelEnum::GEOGRAPHY);
        $m = new Point(1.0, 9007199254740993, 9007199254740993, 9007199254740992.0, 4326, SpatialModelEnum::GEOGRAPHY);

        static::assertFalse($integer->equalsTo($y));
        static::assertFalse($y->equalsTo($integer));
        static::assertFalse($integer->equalsTo($z));
        static::assertFalse($z->equalsTo($integer));
        static::assertFalse($integer->equalsTo($m));
        static::assertFalse($m->equalsTo($integer));
        static::assertSame([1, 9007199254740993, 9007199254740993, 9007199254740993], $integer->toArray());
        static::assertSame([1.0, 9007199254740993, 9007199254740993, 9007199254740992.0], $m->toArray());
    }

    /** The negative integer limit is exactly representable, unlike its neighbour. */
    public function testNegativeIntegerLimitAndNeighbour(): void
    {
        $limit = new PointImmutable(\PHP_INT_MIN, 2);
        $floating = new PointImmutable(-9223372036854775808.0, 2.0);
        $neighbour = new PointImmutable(-9223372036854775807, 2);
        $outside = new PointImmutable(-9223372036854777856.0, 2.0);

        static::assertTrue($limit->equalsTo($floating));
        static::assertTrue($floating->equalsTo($limit));
        static::assertFalse($neighbour->equalsTo($floating));
        static::assertFalse($floating->equalsTo($neighbour));
        static::assertFalse($limit->equalsTo($outside));
        static::assertFalse($outside->equalsTo($limit));
        static::assertSame([\PHP_INT_MIN, 2], $limit->toArray());
        static::assertSame([-9223372036854775807, 2], $neighbour->toArray());
    }

    /** A rounded positive integer limit is outside the integer domain. */
    public function testPositiveIntegerLimitIsNotItsRoundedFloat(): void
    {
        $integer = new PointImmutable(\PHP_INT_MAX, 2);
        $outside = new PointImmutable(9223372036854775808.0, 2.0);
        $insideInteger = new PointImmutable(9223372036854774784, 2);
        $insideFloating = new PointImmutable(9223372036854774784.0, 2.0);

        static::assertFalse($integer->equalsTo($outside));
        static::assertFalse($outside->equalsTo($integer));
        static::assertTrue($insideInteger->equalsTo($insideFloating));
        static::assertTrue($insideFloating->equalsTo($insideInteger));
        static::assertSame([\PHP_INT_MAX, 2], $integer->toArray());
        static::assertSame([9223372036854775808.0, 2.0], $outside->toArray());
    }
}
