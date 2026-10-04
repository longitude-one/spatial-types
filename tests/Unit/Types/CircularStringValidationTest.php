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

use LongitudeOne\SpatialTypes\Exception\InvalidDimensionException;
use LongitudeOne\SpatialTypes\Exception\InvalidFamilyException;
use LongitudeOne\SpatialTypes\Exception\InvalidSridException;
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

    /** Coordinate dimensions cannot be mixed. */
    public function testRejectDifferentDimension(): void
    {
        $this->expectException(InvalidDimensionException::class);
        new CircularString([new Point(0, 0), new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point(1, 1, 1), new Point(2, 0)]);
    }

    /** Geometry and geography points cannot be mixed. */
    public function testRejectDifferentFamily(): void
    {
        $this->expectException(InvalidFamilyException::class);
        new CircularString([new Point(0, 0), new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\Point(1, 1), new Point(2, 0)]);
    }

    /** SRID zero is not a wildcard. */
    public function testRejectDifferentReference(): void
    {
        $this->expectException(InvalidSridException::class);
        new CircularString([new Point(0, 0, 4326), new Point(1, 1), new Point(2, 0, 4326)], 4326);
    }

    /** A matching tuple length does not make XYZ and XYM compatible. */
    public function testRejectElevationPointInMeasuredCurve(): void
    {
        $this->expectException(InvalidDimensionException::class);
        new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\CircularString([
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point(0, 0, 1),
            new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\Point(1, 1, 1),
            new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\Point(2, 0, 1),
        ]);
    }

    /** Empty members cannot define an arc. */
    public function testRejectEmptyPoint(): void
    {
        $this->expectException(InvalidValueException::class);
        new CircularString([new Point(), new Point(1, 1), new Point(2, 0)]);
    }

    /** Four points leave an incomplete second arc. */
    public function testRejectEvenPointCount(): void
    {
        $this->expectException(InvalidValueException::class);
        new CircularString([[0, 0], [1, 1], [2, 0], [3, 1]]);
    }

    /** An intermediate point must differ from the end point. */
    public function testRejectIntermediateEqualToEnd(): void
    {
        $this->expectException(InvalidValueException::class);
        new CircularString([[0, 0], [2, 0], [2, 0]]);
    }

    /** An intermediate point must differ from the start point. */
    public function testRejectIntermediateEqualToStart(): void
    {
        $this->expectException(InvalidValueException::class);
        new CircularString([[0, 0], [0, 0], [2, 0]]);
    }

    /** Non-point elements are invalid. */
    public function testRejectInvalidMember(): void
    {
        $this->expectException(InvalidValueException::class);
        (new \ReflectionClass(CircularString::class))->newInstance([false, [1, 1], [2, 0]]);
    }

    /** One point cannot define an arc. */
    public function testRejectOnePoint(): void
    {
        $this->expectException(InvalidValueException::class);
        new CircularString([[0, 0]]);
    }

    /** Two points cannot define an arc. */
    public function testRejectTwoPoints(): void
    {
        $this->expectException(InvalidValueException::class);
        new CircularString([[0, 0], [1, 1]]);
    }
}
