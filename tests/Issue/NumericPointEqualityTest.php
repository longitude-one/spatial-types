<?php
/**
 * This file is part of the spatial project.
 *
 * PHP 8.4 | 8.5
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2026
 * Copyright Longitude One 2026
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\SpatialTypes\Tests\Issue;

use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\PointImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use PHPUnit\Framework\TestCase;

/** Approved representation equality from Story #64.
 * @internal
 *
 * @coversNothing
 */
final class NumericPointEqualityTest extends TestCase
{
    /** Representation context and concrete classes are not normalized. */
    public function testContextLayoutAndClassDistinctions(): void
    {
        $point = new PointImmutable(1, 2, 4326);
        $otherReference = new PointImmutable(1.0, 2.0, 3857);
        $geography = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(1.0, 2.0, 4326);
        $mutable = new Point(1.0, 2.0, 4326);
        $xyzNull = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1.0, 2.0, null, 4326);
        $xyzZero = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1.0, 2.0, 0.0, 4326);
        $xymNull = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1.0, 2.0, null, 4326);
        $xymZero = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1.0, 2.0, 0.0, 4326);
        $xyzm = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1.0, 2.0, 0.0, null, 4326);

        static::assertFalse($point->equalsTo($otherReference));
        static::assertFalse($otherReference->equalsTo($point));
        static::assertFalse($point->equalsTo($geography));
        static::assertFalse($geography->equalsTo($point));
        static::assertFalse($point->equalsTo($mutable));
        static::assertFalse($mutable->equalsTo($point));
        static::assertFalse($point->equalsTo($xyzNull));
        static::assertFalse($xyzNull->equalsTo($point));
        static::assertFalse($point->equalsTo($xyzZero));
        static::assertFalse($point->equalsTo($xymNull));
        static::assertFalse($point->equalsTo($xymZero));
        static::assertFalse($point->equalsTo($xyzm));
        static::assertFalse($xyzNull->equalsTo($xymNull));
        static::assertFalse($xymZero->equalsTo($xyzZero));
    }

    /** Different values on each axis remain unequal without a tolerance. */
    public function testDifferentNumericValues(): void
    {
        $point = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1, 2, 3, 4);

        static::assertFalse($point->equalsTo(new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1.000000000000001, 2.0, 3.0, 4.0)));
        static::assertFalse($point->equalsTo(new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1.0, 2.5, 3.0, 4.0)));
        static::assertFalse($point->equalsTo(new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1.0, 2.0, 3.5, 4.0)));
        static::assertFalse($point->equalsTo(new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1.0, 2.0, 3.0, 4.5)));
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension2GeographyImmutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(1, 2, 4326);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(1.0, 2.0, 4326);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2], $integer->toArray());
        static::assertSame([1.0, 2.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension2GeographyMutableNumericEquivalence(): void
    {
        $integer = new Point(1, 2, 4326, SpatialModelEnum::GEOGRAPHY);
        $floating = new Point(1.0, 2.0, 4326, SpatialModelEnum::GEOGRAPHY);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2], $integer->toArray());
        static::assertSame([1.0, 2.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension2GeometryImmutableNumericEquivalence(): void
    {
        $integer = new PointImmutable(1, 2, 4326);
        $floating = new PointImmutable(1.0, 2.0, 4326);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2], $integer->toArray());
        static::assertSame([1.0, 2.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension2GeometryMutableNumericEquivalence(): void
    {
        $integer = new Point(1, 2, 4326, SpatialModelEnum::GEOMETRY);
        $floating = new Point(1.0, 2.0, 4326, SpatialModelEnum::GEOMETRY);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2], $integer->toArray());
        static::assertSame([1.0, 2.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension3mGeographyImmutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(1, 2, 4, 4326);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geography\PointImmutable(1.0, 2.0, 4.0, 4326);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 4], $integer->toArray());
        static::assertSame([1.0, 2.0, 4.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension3mGeographyMutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1, 2, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1.0, 2.0, 4.0, 4326, SpatialModelEnum::GEOGRAPHY);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 4], $integer->toArray());
        static::assertSame([1.0, 2.0, 4.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension3mGeometryImmutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1, 2, 4, 4326);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Geometry\PointImmutable(1.0, 2.0, 4.0, 4326);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 4], $integer->toArray());
        static::assertSame([1.0, 2.0, 4.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension3mGeometryMutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1, 2, 4, 4326, SpatialModelEnum::GEOMETRY);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1.0, 2.0, 4.0, 4326, SpatialModelEnum::GEOMETRY);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 4], $integer->toArray());
        static::assertSame([1.0, 2.0, 4.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension3zGeographyImmutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(1, 2, 3, 4326);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geography\PointImmutable(1.0, 2.0, 3.0, 4326);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 3], $integer->toArray());
        static::assertSame([1.0, 2.0, 3.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension3zGeographyMutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(1, 2, 3, 4326, SpatialModelEnum::GEOGRAPHY);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(1.0, 2.0, 3.0, 4326, SpatialModelEnum::GEOGRAPHY);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 3], $integer->toArray());
        static::assertSame([1.0, 2.0, 3.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension3zGeometryImmutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1, 2, 3, 4326);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1.0, 2.0, 3.0, 4326);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 3], $integer->toArray());
        static::assertSame([1.0, 2.0, 3.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension3zGeometryMutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(1, 2, 3, 4326, SpatialModelEnum::GEOMETRY);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Point(1.0, 2.0, 3.0, 4326, SpatialModelEnum::GEOMETRY);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 3], $integer->toArray());
        static::assertSame([1.0, 2.0, 3.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension4zmGeographyImmutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(1, 2, 3, 4, 4326);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geography\PointImmutable(1.0, 2.0, 3.0, 4.0, 4326);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 3, 4], $integer->toArray());
        static::assertSame([1.0, 2.0, 3.0, 4.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension4zmGeographyMutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(1, 2, 3, 4, 4326, SpatialModelEnum::GEOGRAPHY);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(1.0, 2.0, 3.0, 4.0, 4326, SpatialModelEnum::GEOGRAPHY);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 3, 4], $integer->toArray());
        static::assertSame([1.0, 2.0, 3.0, 4.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension4zmGeometryImmutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1, 2, 3, 4, 4326);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Geometry\PointImmutable(1.0, 2.0, 3.0, 4.0, 4326);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 3, 4], $integer->toArray());
        static::assertSame([1.0, 2.0, 3.0, 4.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** Numeric storage does not affect otherwise identical representations. */
    public function testDimension4zmGeometryMutableNumericEquivalence(): void
    {
        $integer = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(1, 2, 3, 4, 4326, SpatialModelEnum::GEOMETRY);
        $floating = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\Point(1.0, 2.0, 3.0, 4.0, 4326, SpatialModelEnum::GEOMETRY);

        static::assertTrue($integer->equalsTo($floating));
        static::assertTrue($floating->equalsTo($integer));
        static::assertSame([1, 2, 3, 4], $integer->toArray());
        static::assertSame([1.0, 2.0, 3.0, 4.0], $floating->toArray());
        static::assertSame(4326, $integer->getSrid());
        static::assertSame(4326, $floating->getSrid());
    }

    /** EMPTY equality retains context and differs from supplied positions. */
    public function testEmptyRepresentationDistinctions(): void
    {
        $empty = new PointImmutable(null, null, 4326);
        $same = new PointImmutable(null, null, 4326);
        $reference = new PointImmutable(null, null, 3857);
        $geography = new \LongitudeOne\SpatialTypes\Types\Dimension2\Geography\PointImmutable(null, null, 4326);
        $xyz = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(null, null, null, 4326);
        $position = new PointImmutable(0.0, 0.0, 4326);
        $partial = new PointImmutable(0, null, 4326);

        static::assertTrue($empty->equalsTo($same));
        static::assertFalse($empty->equalsTo($reference));
        static::assertFalse($empty->equalsTo($geography));
        static::assertFalse($empty->equalsTo($xyz));
        static::assertFalse($empty->equalsTo($position));
        static::assertFalse($position->equalsTo($empty));
        static::assertFalse($partial->equalsTo($empty));
        static::assertSame([], $empty->toArray());
        static::assertSame([0.0, 0.0], $position->toArray());
    }

    /** Mutable context uses instance metadata instead of class names. */
    public function testMutableContextDistinctions(): void
    {
        $geometry = new Point(1, 2, 4326);
        $geography = new Point(1.0, 2.0, 4326, SpatialModelEnum::GEOGRAPHY);
        $reference = new Point(1.0, 2.0, 3857);

        static::assertFalse($geometry->equalsTo($geography));
        static::assertFalse($geography->equalsTo($geometry));
        static::assertFalse($geometry->equalsTo($reference));
        static::assertFalse($reference->equalsTo($geometry));
    }

    /** Missing ordinates remain distinct from numeric zero in every axis. */
    public function testNullOrdinateDistinctions(): void
    {
        $partialX = new PointImmutable(null, 2);
        $partialY = new PointImmutable(1, null);
        $zeroX = new PointImmutable(0.0, 2.0);
        $zeroY = new PointImmutable(1.0, 0.0);
        $partialZ = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1, 2, null);
        $zeroZ = new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1.0, 2.0, 0.0);
        $partialM = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1, 2, null);
        $zeroM = new \LongitudeOne\SpatialTypes\Types\Dimension3m\Point(1.0, 2.0, 0.0);

        static::assertFalse($partialX->equalsTo($zeroX));
        static::assertFalse($zeroX->equalsTo($partialX));
        static::assertFalse($partialY->equalsTo($zeroY));
        static::assertFalse($zeroY->equalsTo($partialY));
        static::assertFalse($partialZ->equalsTo($zeroZ));
        static::assertFalse($zeroZ->equalsTo($partialZ));
        static::assertFalse($partialM->equalsTo($zeroM));
        static::assertFalse($zeroM->equalsTo($partialM));
        static::assertTrue($partialX->equalsTo(new PointImmutable(null, 2.0)));
        static::assertTrue($partialZ->equalsTo(new \LongitudeOne\SpatialTypes\Types\Dimension3z\Geometry\PointImmutable(1.0, 2.0, null)));
        static::assertSame([null, 2], $partialX->toArray());
        static::assertSame([1, 2, null], $partialZ->toArray());
        static::assertSame([1, 2, null], $partialM->toArray());
    }
}
