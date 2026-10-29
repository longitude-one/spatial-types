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

use LongitudeOne\Core\Enum\CoordinateDimensionEnum;
use LongitudeOne\Core\Enum\GeometryTypeEnum;
use LongitudeOne\Core\Enum\SpatialModelEnum;
use LongitudeOne\SpatialTypes\Exception\InvalidValueException;
use LongitudeOne\SpatialTypes\Exception\OutOfBoundsException;
use LongitudeOne\SpatialTypes\Interfaces\MutableCompoundCurveInterface;
use LongitudeOne\SpatialTypes\Interfaces\MutableSpatialInterface;
use LongitudeOne\SpatialTypes\Reference\SpatialReference;
use LongitudeOne\SpatialTypes\Types\Dimension2\CircularString;
use LongitudeOne\SpatialTypes\Types\Dimension2\CompoundCurve;
use LongitudeOne\SpatialTypes\Types\Dimension2\Geometry\LineStringImmutable;
use LongitudeOne\SpatialTypes\Types\Dimension2\LineString;
use LongitudeOne\SpatialTypes\Types\Dimension2\Point;
use PHPUnit\Framework\TestCase;

/** Mutable compound membership, ownership and context.
 * @internal
 *
 * @coversNothing
 */
class MutableCompoundCurveTest extends TestCase
{
    /** Invalid addressing leaves components unchanged. */
    public function testCountAccessIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $curve = new LineString([$point], 4326);
        $compound = new CompoundCurve([$curve], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->getCurve(1);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[1, 2]]], $compound->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $curve->getSrid());
            static::assertSame(2154, $compound->getSrid());
        }
    }

    /** Invalid addressing leaves components unchanged. */
    public function testCountRemovalIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $curve = new LineString([$point], 4326);
        $compound = new CompoundCurve([$curve], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->removeCurve(1);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[1, 2]]], $compound->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $curve->getSrid());
            static::assertSame(2154, $compound->getSrid());
        }
    }

    /** Invalid addressing leaves components unchanged. */
    public function testCountReplacementIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $curve = new LineString([$point], 4326);
        $compound = new CompoundCurve([$curve], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->setCurve(1, $curve);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[1, 2]]], $compound->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $curve->getSrid());
            static::assertSame(2154, $compound->getSrid());
        }
    }

    /** Functional context compatibility is left to explicit validation. */
    public function testDifferentComponentLayoutsRemainRepresentable(): void
    {
        $line = new LineString([[0, null]], 4326);
        $arc = new \LongitudeOne\SpatialTypes\Types\Dimension3m\CircularString([[9, 8, null]], 2154, SpatialModelEnum::GEOGRAPHY);
        $compound = new CompoundCurve([$line, $arc]);
        static::assertSame([[[0, null]], [[9, 8, null]]], $compound->toArray());
        static::assertSame(CoordinateDimensionEnum::XY, $compound->getDimension());
        static::assertSame(CoordinateDimensionEnum::XYM, $arc->getDimension());
        static::assertSame(2154, $arc->getSrid());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $arc->getFamily());
    }

    /** Empty and incomplete components do not trigger geometric checks. */
    public function testEmptyAndIncompleteComponents(): void
    {
        $compound = new CompoundCurve();
        static::assertTrue($compound->isEmpty());
        static::assertNull($compound->getStartPoint());
        static::assertNull($compound->getEndPoint());
        static::assertFalse($compound->isClosed());
        $empty = new LineString([]);
        $partial = new CircularString([[0, null]]);
        $compound->setCurves([$empty, $partial]);
        static::assertFalse($compound->isEmpty());
        static::assertNull($compound->getStartPoint());
        static::assertFalse($compound->isClosed());
        static::assertSame([[], [[0, null]]], $compound->toArray());
        $compound->setCurves([$partial, $empty]);
        static::assertNull($compound->getEndPoint());
        static::assertFalse($compound->isClosed());
        $compound->setCurves([$partial]);
        static::assertTrue($compound->isClosed());
        static::assertSame($compound, $compound->setCurves([]));
        static::assertTrue($compound->isEmpty());
    }

    /** Empty indexed access has no existing component. */
    public function testEmptyIndexedAccess(): void
    {
        $compound = new CompoundCurve();
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->getCurve(0);
        } finally {
            static::assertSame([], $compound->getCurves());
        }
    }

    /** Context updates remain local. */
    public function testFamilyLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new CircularString([$point], $initial);
        $compound = new CompoundCurve([$line, $arc], $initial);
        $owner = new CompoundCurve([$line], $initial);
        static::assertSame($compound, $compound->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $arc->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $owner->getCurve(0)->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        static::assertSame($line, $compound->getCurve(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Context updates reach shared descendants recursively. */
    public function testFamilyPropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new CircularString([$point], $initial);
        $compound = new CompoundCurve([$line, $arc], $initial);
        $owner = new CompoundCurve([$line], $initial);
        static::assertSame($compound, $compound->setFamily(SpatialModelEnum::GEOGRAPHY));
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $arc->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getCurve(0)->getFamily());
        static::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        static::assertSame($line, $compound->getCurve(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Typed mutation rejects immutable components before modification. */
    public function testImmutableAdditionIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($compound, 'addCurve'))->invoke($compound, $immutable);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[0, null]]], $compound->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
        }
    }

    /** Unsupported component input is rejected atomically. */
    public function testImmutableConstructorIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(CompoundCurve::class))->newInstance([$immutable]);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[0, null]]], $compound->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
            static::assertSame(2154, $immutable->getSrid());
            static::assertSame(4326, $curve->getSrid());
        }
    }

    /** Typed mutation rejects immutable components before modification. */
    public function testImmutableInsertionIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($compound, 'insertCurve'))->invoke($compound, 0, $immutable);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[0, null]]], $compound->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
        }
    }

    /** Typed mutation rejects immutable components before modification. */
    public function testImmutableReplacementIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve]);
        $this->expectException(\TypeError::class);

        try {
            (new \ReflectionMethod($compound, 'setCurve'))->invoke($compound, 0, $immutable);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[0, null]]], $compound->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
        }
    }

    /** Unsupported component input is rejected atomically. */
    public function testImmutableSequenceReplacementIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionMethod($compound, 'setCurves'))->invoke($compound, [$curve, $immutable]);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[0, null]]], $compound->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
            static::assertSame(2154, $immutable->getSrid());
            static::assertSame(4326, $curve->getSrid());
        }
    }

    /** Invalid addressing leaves components unchanged. */
    public function testNegativeAccessIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $curve = new LineString([$point], 4326);
        $compound = new CompoundCurve([$curve], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->getCurve(-1);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[1, 2]]], $compound->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $curve->getSrid());
            static::assertSame(2154, $compound->getSrid());
        }
    }

    /** Invalid addressing leaves components unchanged. */
    public function testNegativeInsertionIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $curve = new LineString([$point], 4326);
        $compound = new CompoundCurve([$curve], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->insertCurve(-1, $curve);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[1, 2]]], $compound->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $curve->getSrid());
            static::assertSame(2154, $compound->getSrid());
        }
    }

    /** Invalid addressing leaves components unchanged. */
    public function testNegativeRemovalIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $curve = new LineString([$point], 4326);
        $compound = new CompoundCurve([$curve], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->removeCurve(-1);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[1, 2]]], $compound->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $curve->getSrid());
            static::assertSame(2154, $compound->getSrid());
        }
    }

    /** Invalid addressing leaves components unchanged. */
    public function testNegativeReplacementIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $curve = new LineString([$point], 4326);
        $compound = new CompoundCurve([$curve], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->setCurve(-1, $curve);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[1, 2]]], $compound->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $curve->getSrid());
            static::assertSame(2154, $compound->getSrid());
        }
    }

    /** Unsupported component input is rejected atomically. */
    public function testNestedCompoundIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionMethod($compound, 'setCurves'))->invoke($compound, [$curve, new CompoundCurve()]);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[0, null]]], $compound->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
            static::assertSame(2154, $immutable->getSrid());
            static::assertSame(4326, $curve->getSrid());
        }
    }

    /** Membership retains component types and reference identity. */
    public function testOrderedMembershipAndSharedEdits(): void
    {
        $point = new Point(0, null, 4326);
        $line = new LineString([$point], 4326);
        $arc = new CircularString([[9, 8]], 2154);
        $other = new LineString([[3, 4]]);
        $compound = new CompoundCurve([$line]);
        $owner = new CompoundCurve([$line]);
        static::assertSame($compound, $compound->addCurve($arc)->insertCurve(1, $other));
        static::assertSame([$line, $other, $arc], $compound->getCurves());
        static::assertSame($compound, $compound->insertCurve(3, $line));
        static::assertSame($compound, $compound->setCurve(1, $arc)->removeCurve(2));
        static::assertSame([$line, $arc, $line], $compound->getElements());
        static::assertSame($arc, $compound->getCurve(1));
        static::assertSame($point, $compound->getStartPoint());
        static::assertSame($point, $compound->getEndPoint());
        $point->setY(7);
        $line->addPoint(new Point(null, 5));
        static::assertSame([[[0, 7], [null, 5]]], $owner->toArray());
        static::assertSame([[[0, 7], [null, 5]], [[9, 8]], [[0, 7], [null, 5]]], $compound->toArray());
        static::assertSame($compound, $compound->setCurves([7 => $arc, 9 => $line]));
        static::assertSame([$arc, $line], $compound->getCurves());
        $returned = $compound->getCurves();
        $returned[0] = $other;
        static::assertSame($arc, $compound->getCurve(0));
    }

    /** Invalid addressing leaves components unchanged. */
    public function testOversizedInsertionIsAtomic(): void
    {
        $point = new Point(1, 2, 4326);
        $curve = new LineString([$point], 4326);
        $compound = new CompoundCurve([$curve], 2154);
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->insertCurve(2, $curve);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[1, 2]]], $compound->toArray());
            static::assertSame(4326, $point->getSrid());
            static::assertSame(4326, $curve->getSrid());
            static::assertSame(2154, $compound->getSrid());
        }
    }

    /** Mutable public contracts expose no copy or dimension mutation. */
    public function testPublicContracts(): void
    {
        $compound = new CompoundCurve();
        static::assertContains(MutableCompoundCurveInterface::class, class_implements($compound));
        static::assertContains(MutableSpatialInterface::class, class_implements($compound));
        static::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        static::assertSame(CoordinateDimensionEnum::XY, $compound->getDimension());
        static::assertSame(SpatialModelEnum::GEOMETRY, $compound->getFamily());
        static::assertFalse((new \ReflectionClass($compound))->hasMethod('withSrid'));
        static::assertFalse((new \ReflectionClass($compound))->hasMethod('withSpatialReference'));
        static::assertFalse((new \ReflectionClass($compound))->hasMethod('withCurves'));
        static::assertFalse((new \ReflectionClass($compound))->hasMethod('setDimension'));
        static::assertSame('static', (string) (new \ReflectionMethod($compound, 'setCurves'))->getReturnType());
    }

    /** Context updates remain local. */
    public function testReferenceLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new CircularString([$point], $initial);
        $compound = new CompoundCurve([$line, $arc], $initial);
        $owner = new CompoundCurve([$line], $initial);
        static::assertSame($compound, $compound->setSpatialReference($reference, false));
        static::assertSame($reference, $compound->getSpatialReference());
        static::assertSame($initial, $line->getSpatialReference());
        static::assertSame($initial, $arc->getSpatialReference());
        static::assertSame($initial, $point->getSpatialReference());
        static::assertSame($initial, $owner->getCurve(0)->getSpatialReference());
        static::assertSame($initial, $owner->getSpatialReference());
        static::assertSame($line, $compound->getCurve(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Context updates reach shared descendants recursively. */
    public function testReferencePropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $reference = SpatialReference::epsg(2154);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new CircularString([$point], $initial);
        $compound = new CompoundCurve([$line, $arc], $initial);
        $owner = new CompoundCurve([$line], $initial);
        static::assertSame($compound, $compound->setSpatialReference($reference));
        static::assertSame($reference, $compound->getSpatialReference());
        static::assertSame($reference, $line->getSpatialReference());
        static::assertSame($reference, $arc->getSpatialReference());
        static::assertSame($reference, $point->getSpatialReference());
        static::assertSame($reference, $owner->getCurve(0)->getSpatialReference());
        static::assertSame($initial, $owner->getSpatialReference());
        static::assertSame($line, $compound->getCurve(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Context updates remain local. */
    public function testSridLocalOnly(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new CircularString([$point], $initial);
        $compound = new CompoundCurve([$line, $arc], $initial);
        $owner = new CompoundCurve([$line], $initial);
        static::assertSame($compound, $compound->setSrid(3857, false));
        static::assertSame(3857, $compound->getSrid());
        static::assertSame(4326, $line->getSrid());
        static::assertSame(4326, $arc->getSrid());
        static::assertSame(4326, $point->getSrid());
        static::assertSame(4326, $owner->getCurve(0)->getSrid());
        static::assertSame(4326, $owner->getSrid());
        static::assertSame($line, $compound->getCurve(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Context updates reach shared descendants recursively. */
    public function testSridPropagation(): void
    {
        $initial = SpatialReference::epsg(4326);
        $point = new Point(0, null, $initial);
        $line = new LineString([$point], $initial);
        $arc = new CircularString([$point], $initial);
        $compound = new CompoundCurve([$line, $arc], $initial);
        $owner = new CompoundCurve([$line], $initial);
        static::assertSame($compound, $compound->setSrid(3857));
        static::assertSame(3857, $compound->getSrid());
        static::assertSame(3857, $line->getSrid());
        static::assertSame(3857, $arc->getSrid());
        static::assertSame(3857, $point->getSrid());
        static::assertSame(3857, $owner->getCurve(0)->getSrid());
        static::assertSame(4326, $owner->getSrid());
        static::assertSame($line, $compound->getCurve(0));
        static::assertSame($point, $arc->getPoint(0));
        static::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Unsupported component input is rejected atomically. */
    public function testTupleConstructorIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            (new \ReflectionClass(CompoundCurve::class))->newInstance([[[1, 2]]]);
        } finally {
            static::assertSame([$curve], $compound->getCurves());
            static::assertSame([[[0, null]]], $compound->toArray());
            static::assertSame([[3, 4]], $immutable->toArray());
            static::assertSame(2154, $immutable->getSrid());
            static::assertSame(4326, $curve->getSrid());
        }
    }

    /** XYM layout preserves every supplied ordinate. */
    public function testXymLayout(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, null, 9]], 4326);
        $arc = new \LongitudeOne\SpatialTypes\Types\Dimension3m\CircularString([[0, null, 9]], 2154);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3m\CompoundCurve([$line, $arc], 0, SpatialModelEnum::GEOGRAPHY);
        static::assertSame(CoordinateDimensionEnum::XYM, $compound->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame([[[0, null, 9]], [[0, null, 9]]], $compound->toArray());
        static::assertSame($compound, $compound->setCurve(0, $arc)->setSrid(3857)->setFamily(SpatialModelEnum::GEOMETRY));
        static::assertSame(CoordinateDimensionEnum::XYM, $compound->getDimension());
        static::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        static::assertSame([[[0, null, 9]], [[0, null, 9]]], $compound->toArray());
        static::assertSame(4326, $line->getSrid());
        static::assertSame(3857, $arc->getPoint(0)->getSrid());
    }

    /** XYZ layout preserves every supplied ordinate. */
    public function testXyzLayout(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, null, 8]], 4326);
        $arc = new \LongitudeOne\SpatialTypes\Types\Dimension3z\CircularString([[0, null, 8]], 2154);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3z\CompoundCurve([$line, $arc], 0, SpatialModelEnum::GEOGRAPHY);
        static::assertSame(CoordinateDimensionEnum::XYZ, $compound->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame([[[0, null, 8]], [[0, null, 8]]], $compound->toArray());
        static::assertSame($compound, $compound->setCurve(0, $arc)->setSrid(3857)->setFamily(SpatialModelEnum::GEOMETRY));
        static::assertSame(CoordinateDimensionEnum::XYZ, $compound->getDimension());
        static::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        static::assertSame([[[0, null, 8]], [[0, null, 8]]], $compound->toArray());
        static::assertSame(4326, $line->getSrid());
        static::assertSame(3857, $arc->getPoint(0)->getSrid());
    }

    /** XYZM layout preserves every supplied ordinate. */
    public function testXyzmLayout(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, null, 8, 9]], 4326);
        $arc = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\CircularString([[0, null, 8, 9]], 2154);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\CompoundCurve([$line, $arc], 0, SpatialModelEnum::GEOGRAPHY);
        static::assertSame(CoordinateDimensionEnum::XYZM, $compound->getDimension());
        static::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        static::assertSame([[[0, null, 8, 9]], [[0, null, 8, 9]]], $compound->toArray());
        static::assertSame($compound, $compound->setCurve(0, $arc)->setSrid(3857)->setFamily(SpatialModelEnum::GEOMETRY));
        static::assertSame(CoordinateDimensionEnum::XYZM, $compound->getDimension());
        static::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        static::assertSame([[[0, null, 8, 9]], [[0, null, 8, 9]]], $compound->toArray());
        static::assertSame(4326, $line->getSrid());
        static::assertSame(3857, $arc->getPoint(0)->getSrid());
    }
}
