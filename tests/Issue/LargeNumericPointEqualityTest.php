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

        self::assertFalse($curve->isClosed());
        self::assertSame([[9007199254740993, 2], [0, 0], [9007199254740992.0, 2]], $curve->toArray());
        $start = $curve->getStartPoint();
        $end = $curve->getEndPoint();
        self::assertNotNull($start);
        self::assertNotNull($end);
        self::assertSame(9007199254740993, $start->getX());
        self::assertSame(9007199254740992.0, $end->getX());
    }

    /** Reproduce the reviewed multipoint without merging its two member positions. */
    public function testDistinctLargeMembersRemainSimple(): void
    {
        $integer = new PointImmutable(9007199254740993, 2);
        $floating = new PointImmutable(9007199254740992.0, 2);
        $points = new MultiPointImmutable([$integer, $floating]);

        self::assertTrue($points->isSimple());
        self::assertSame($integer, $points->getPoint(0));
        self::assertSame($floating, $points->getPoint(1));
        self::assertSame([[9007199254740993, 2], [9007199254740992.0, 2]], $points->toArray());
        self::assertSame([9007199254740993, 2], $integer->toArray());
        self::assertSame([9007199254740992.0, 2], $floating->toArray());
    }

    /** Negative values must not be merged by the same binary64 rounding. */
    public function testDistinctNegativeValuesBeyondBinary64ExactIntegerRange(): void
    {
        $integer = new PointImmutable(-9007199254740993, 2);
        $floating = new PointImmutable(-9007199254740992.0, 2);

        self::assertFalse($integer->equalsTo($floating));
        self::assertFalse($floating->equalsTo($integer));
        self::assertSame([-9007199254740993, 2], $integer->toArray());
        self::assertSame([-9007199254740992.0, 2], $floating->toArray());
    }

    /** Reproduce the exact distinct representations identified in review. */
    public function testDistinctValuesAboveBinary64ExactIntegerRange(): void
    {
        $integer = new PointImmutable(9007199254740993, 2);
        $floating = new PointImmutable(9007199254740992.0, 2);

        self::assertFalse($integer->equalsTo($floating));
        self::assertFalse($floating->equalsTo($integer));
        self::assertSame([9007199254740993, 2], $integer->toArray());
        self::assertSame([9007199254740992.0, 2], $floating->toArray());
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

        self::assertTrue($boundaryInteger->equalsTo($boundaryFloating));
        self::assertTrue($boundaryFloating->equalsTo($boundaryInteger));
        self::assertTrue($aboveInteger->equalsTo($aboveFloating));
        self::assertTrue($aboveFloating->equalsTo($aboveInteger));
        self::assertTrue($negativeInteger->equalsTo($negativeFloating));
        self::assertTrue($negativeFloating->equalsTo($negativeInteger));
        self::assertSame([9007199254740994, 2], $aboveInteger->toArray());
        self::assertSame([9007199254740994.0, 2.0], $aboveFloating->toArray());
    }

    /** A fractional floating ordinate must not be truncated into equality. */
    public function testFractionalValuesAndSignedZero(): void
    {
        $integer = new PointImmutable(1, 2);
        $fractional = new PointImmutable(1.5, 2.0);
        $zero = new PointImmutable(0, 2);
        $negativeZero = new PointImmutable(-0.0, 2.0);

        self::assertFalse($integer->equalsTo($fractional));
        self::assertFalse($fractional->equalsTo($integer));
        self::assertTrue($zero->equalsTo($negativeZero));
        self::assertTrue($negativeZero->equalsTo($zero));
        self::assertSame([1.5, 2.0], $fractional->toArray());
    }

    /** All represented axes share the exact mixed comparison. */
    public function testLargeDistinctMutableGeographyOrdinates(): void
    {
        $integer = new Point(1, 9007199254740993, 9007199254740993, 9007199254740993, 4326, SpatialModelEnum::GEOGRAPHY);
        $y = new Point(1.0, 9007199254740992.0, 9007199254740993, 9007199254740993, 4326, SpatialModelEnum::GEOGRAPHY);
        $z = new Point(1.0, 9007199254740993, 9007199254740992.0, 9007199254740993, 4326, SpatialModelEnum::GEOGRAPHY);
        $m = new Point(1.0, 9007199254740993, 9007199254740993, 9007199254740992.0, 4326, SpatialModelEnum::GEOGRAPHY);

        self::assertFalse($integer->equalsTo($y));
        self::assertFalse($y->equalsTo($integer));
        self::assertFalse($integer->equalsTo($z));
        self::assertFalse($z->equalsTo($integer));
        self::assertFalse($integer->equalsTo($m));
        self::assertFalse($m->equalsTo($integer));
        self::assertSame([1, 9007199254740993, 9007199254740993, 9007199254740993], $integer->toArray());
        self::assertSame([1.0, 9007199254740993, 9007199254740993, 9007199254740992.0], $m->toArray());
    }

    /** The negative integer limit is exactly representable, unlike its neighbour. */
    public function testNegativeIntegerLimitAndNeighbour(): void
    {
        $limit = new PointImmutable(PHP_INT_MIN, 2);
        $floating = new PointImmutable(-9223372036854775808.0, 2.0);
        $neighbour = new PointImmutable(-9223372036854775807, 2);
        $outside = new PointImmutable(-9223372036854777856.0, 2.0);

        self::assertTrue($limit->equalsTo($floating));
        self::assertTrue($floating->equalsTo($limit));
        self::assertFalse($neighbour->equalsTo($floating));
        self::assertFalse($floating->equalsTo($neighbour));
        self::assertFalse($limit->equalsTo($outside));
        self::assertFalse($outside->equalsTo($limit));
        self::assertSame([PHP_INT_MIN, 2], $limit->toArray());
        self::assertSame([-9223372036854775807, 2], $neighbour->toArray());
    }

    /** A rounded positive integer limit is outside the integer domain. */
    public function testPositiveIntegerLimitIsNotItsRoundedFloat(): void
    {
        $integer = new PointImmutable(PHP_INT_MAX, 2);
        $outside = new PointImmutable(9223372036854775808.0, 2.0);
        $insideInteger = new PointImmutable(9223372036854774784, 2);
        $insideFloating = new PointImmutable(9223372036854774784.0, 2.0);

        self::assertFalse($integer->equalsTo($outside));
        self::assertFalse($outside->equalsTo($integer));
        self::assertTrue($insideInteger->equalsTo($insideFloating));
        self::assertTrue($insideFloating->equalsTo($insideInteger));
        self::assertSame([PHP_INT_MAX, 2], $integer->toArray());
        self::assertSame([9223372036854775808.0, 2.0], $outside->toArray());
    }
}
