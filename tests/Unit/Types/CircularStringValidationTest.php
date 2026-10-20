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

use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\CurveInterface;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * Circular interpolation invariants and compatible point membership.
 *
 * @internal
 *
 * @coversNothing
 */
class CircularStringValidationTest extends TestCase
{
    /** Collinear defining points remain a valid degenerate arc. */
    public function testCollinearArc(): void
    {
        $curve = new CircularString([[0, 0], [1, 0], [2, 0]]);

        static::assertSame([[0, 0], [1, 0], [2, 0]], $curve->toArray());
    }

    /** A repeated endpoint defines a complete circle. */
    public function testCompleteCircle(): void
    {
        $curve = new CircularString([new Point(1, 0), new Point(-1, 0), new Point(1, 0)]);

        static::assertSame([[1, 0], [-1, 0], [1, 0]], $curve->toArray());
        static::assertTrue($curve->getPoint(0)->equalsTo($curve->getPoint(-1)));
    }

    /** Accessing an empty curve fails explicitly. */
    public function testEmptyPointAccess(): void
    {
        $this->expectException(OutOfBoundsException::class);
        (new CircularString([]))->getPoint(0);
    }

    /** Line strings share the approved curve category. */
    public function testLineStringIsCurve(): void
    {
        static::assertContains(CurveInterface::class, class_implements(new LineString([[0, 0], [1, 1]])));
    }

    /** Two arcs share their connecting endpoint without duplicating it. */
    public function testMultipleArcs(): void
    {
        $curve = new CircularString([[0, 0], [1, 1], [2, 0], [3, -1], [4, 0]]);

        static::assertSame([[0, 0], [1, 1], [2, 0], [3, -1], [4, 0]], $curve->toArray());
        static::assertCount(5, $curve->getPoints());
    }

    /** Non-point elements are invalid. */
    public function testRejectInvalidMember(): void
    {
        $this->expectException(InvalidValueException::class);
        (new \ReflectionClass(CircularString::class))->newInstance([false, [1, 1], [2, 0]]);
    }
}
