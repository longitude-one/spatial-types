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
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[1, 2]]], $compound->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame(2154, $compound->getSrid());
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
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[1, 2]]], $compound->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame(2154, $compound->getSrid());
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
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[1, 2]]], $compound->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame(2154, $compound->getSrid());
        }
    }

    /** Matching layouts retain differing family and reference contexts. */
    public function testDifferentComponentContextsRemainRepresentable(): void
    {
        $line = new LineString([[0, null]], 4326);
        $arc = new CircularString([[9, 8]], 2154, SpatialModelEnum::GEOGRAPHY);
        $compound = new CompoundCurve([$line, $arc]);
        self::assertSame([[[0, null]], [[9, 8]]], $compound->toArray());
        self::assertSame(CoordinateDimensionEnum::XY, $compound->getDimension());
        self::assertSame(CoordinateDimensionEnum::XY, $arc->getDimension());
        self::assertSame(2154, $arc->getSrid());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $arc->getFamily());
    }

    /** Empty and incomplete components do not trigger geometric checks. */
    public function testEmptyAndIncompleteComponents(): void
    {
        $compound = new CompoundCurve();
        self::assertNull($compound->getStartPoint());
        self::assertNull($compound->getEndPoint());
        $empty = new LineString([]);
        $partial = new CircularString([[0, null]]);
        $compound->setCurves([$empty, $partial]);
        self::assertNull($compound->getStartPoint());
        self::assertSame([[], [[0, null]]], $compound->toArray());
        $compound->setCurves([$partial, $empty]);
        self::assertNull($compound->getEndPoint());
        $compound->setCurves([$partial]);
        self::assertSame($compound, $compound->setCurves([]));
    }

    /** Empty indexed access has no existing component. */
    public function testEmptyIndexedAccess(): void
    {
        $compound = new CompoundCurve();
        $this->expectException(OutOfBoundsException::class);

        try {
            $compound->getCurve(0);
        } finally {
            self::assertSame([], $compound->getCurves());
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
        self::assertSame($compound, $compound->setFamily(SpatialModelEnum::GEOGRAPHY, false));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $arc->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $owner->getCurve(0)->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        self::assertSame($line, $compound->getCurve(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        self::assertSame($compound, $compound->setFamily(SpatialModelEnum::GEOGRAPHY));
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $line->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $arc->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $point->getFamily());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $owner->getCurve(0)->getFamily());
        self::assertSame(SpatialModelEnum::GEOMETRY, $owner->getFamily());
        self::assertSame($line, $compound->getCurve(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Typed mutation rejects immutable components before modification. */
    public function testImmutableAdditionIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($compound, 'addCurve')->invoke($compound, $immutable);
        } finally {
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[0, null]]], $compound->toArray());
            self::assertSame([[3, 4]], $immutable->toArray());
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
            new \ReflectionClass(CompoundCurve::class)->newInstance([$immutable]);
        } finally {
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[0, null]]], $compound->toArray());
            self::assertSame([[3, 4]], $immutable->toArray());
            self::assertSame(2154, $immutable->getSrid());
            self::assertSame(4326, $curve->getSrid());
        }
    }

    /** Typed mutation rejects immutable components before modification. */
    public function testImmutableInsertionIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($compound, 'insertCurve')->invoke($compound, 0, $immutable);
        } finally {
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[0, null]]], $compound->toArray());
            self::assertSame([[3, 4]], $immutable->toArray());
        }
    }

    /** Typed mutation rejects immutable components before modification. */
    public function testImmutableReplacementIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve]);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionMethod($compound, 'setCurve')->invoke($compound, 0, $immutable);
        } finally {
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[0, null]]], $compound->toArray());
            self::assertSame([[3, 4]], $immutable->toArray());
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
            new \ReflectionMethod($compound, 'setCurves')->invoke($compound, [$curve, $immutable]);
        } finally {
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[0, null]]], $compound->toArray());
            self::assertSame([[3, 4]], $immutable->toArray());
            self::assertSame(2154, $immutable->getSrid());
            self::assertSame(4326, $curve->getSrid());
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
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[1, 2]]], $compound->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame(2154, $compound->getSrid());
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
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[1, 2]]], $compound->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame(2154, $compound->getSrid());
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
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[1, 2]]], $compound->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame(2154, $compound->getSrid());
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
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[1, 2]]], $compound->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame(2154, $compound->getSrid());
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
            new \ReflectionMethod($compound, 'setCurves')->invoke($compound, [$curve, new CompoundCurve()]);
        } finally {
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[0, null]]], $compound->toArray());
            self::assertSame([[3, 4]], $immutable->toArray());
            self::assertSame(2154, $immutable->getSrid());
            self::assertSame(4326, $curve->getSrid());
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
        self::assertSame($compound, $compound->addCurve($arc)->insertCurve(1, $other));
        self::assertSame([$line, $other, $arc], $compound->getCurves());
        self::assertSame($compound, $compound->insertCurve(3, $line));
        self::assertSame($compound, $compound->setCurve(1, $arc)->removeCurve(2));
        self::assertSame([$line, $arc, $line], $compound->getElements());
        self::assertSame($arc, $compound->getCurve(1));
        self::assertSame($point, $compound->getStartPoint());
        self::assertSame($point, $compound->getEndPoint());
        $point->setY(7);
        $line->addPoint(new Point(null, 5));
        self::assertSame([[[0, 7], [null, 5]]], $owner->toArray());
        self::assertSame([[[0, 7], [null, 5]], [[9, 8]], [[0, 7], [null, 5]]], $compound->toArray());
        self::assertSame($compound, $compound->setCurves([7 => $arc, 9 => $line]));
        self::assertSame([$arc, $line], $compound->getCurves());
        $returned = $compound->getCurves();
        $returned[0] = $other;
        self::assertSame($arc, $compound->getCurve(0));
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
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[1, 2]]], $compound->toArray());
            self::assertSame(4326, $point->getSrid());
            self::assertSame(4326, $curve->getSrid());
            self::assertSame(2154, $compound->getSrid());
        }
    }

    /** Mutable public contracts expose no copy or dimension mutation. */
    public function testPublicContracts(): void
    {
        $compound = new CompoundCurve();
        self::assertContains(MutableCompoundCurveInterface::class, class_implements($compound));
        self::assertContains(MutableSpatialInterface::class, class_implements($compound));
        self::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        self::assertSame(CoordinateDimensionEnum::XY, $compound->getDimension());
        self::assertSame(SpatialModelEnum::GEOMETRY, $compound->getFamily());
        self::assertFalse(new \ReflectionClass($compound)->hasMethod('withSrid'));
        self::assertFalse(new \ReflectionClass($compound)->hasMethod('withSpatialReference'));
        self::assertFalse(new \ReflectionClass($compound)->hasMethod('withCurves'));
        self::assertFalse(new \ReflectionClass($compound)->hasMethod('setDimension'));
        self::assertSame('static', (string) new \ReflectionMethod($compound, 'setCurves')->getReturnType());
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
        self::assertSame($compound, $compound->setSpatialReference($reference, false));
        self::assertSame($reference, $compound->getSpatialReference());
        self::assertSame($initial, $line->getSpatialReference());
        self::assertSame($initial, $arc->getSpatialReference());
        self::assertSame($initial, $point->getSpatialReference());
        self::assertSame($initial, $owner->getCurve(0)->getSpatialReference());
        self::assertSame($initial, $owner->getSpatialReference());
        self::assertSame($line, $compound->getCurve(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        self::assertSame($compound, $compound->setSpatialReference($reference));
        self::assertSame($reference, $compound->getSpatialReference());
        self::assertSame($reference, $line->getSpatialReference());
        self::assertSame($reference, $arc->getSpatialReference());
        self::assertSame($reference, $point->getSpatialReference());
        self::assertSame($reference, $owner->getCurve(0)->getSpatialReference());
        self::assertSame($initial, $owner->getSpatialReference());
        self::assertSame($line, $compound->getCurve(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        self::assertSame($compound, $compound->setSrid(3857, false));
        self::assertSame(3857, $compound->getSrid());
        self::assertSame(4326, $line->getSrid());
        self::assertSame(4326, $arc->getSrid());
        self::assertSame(4326, $point->getSrid());
        self::assertSame(4326, $owner->getCurve(0)->getSrid());
        self::assertSame(4326, $owner->getSrid());
        self::assertSame($line, $compound->getCurve(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
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
        self::assertSame($compound, $compound->setSrid(3857));
        self::assertSame(3857, $compound->getSrid());
        self::assertSame(3857, $line->getSrid());
        self::assertSame(3857, $arc->getSrid());
        self::assertSame(3857, $point->getSrid());
        self::assertSame(3857, $owner->getCurve(0)->getSrid());
        self::assertSame(4326, $owner->getSrid());
        self::assertSame($line, $compound->getCurve(0));
        self::assertSame($point, $arc->getPoint(0));
        self::assertSame([[[0, null]], [[0, null]]], $compound->toArray());
    }

    /** Unsupported component input is rejected atomically. */
    public function testTupleConstructorIsRejected(): void
    {
        $curve = new LineString([[0, null]], 4326);
        $immutable = new LineStringImmutable([[3, 4]], 2154);
        $compound = new CompoundCurve([$curve], 4326);
        $this->expectException(InvalidValueException::class);

        try {
            new \ReflectionClass(CompoundCurve::class)->newInstance([[[1, 2]]]);
        } finally {
            self::assertSame([$curve], $compound->getCurves());
            self::assertSame([[[0, null]]], $compound->toArray());
            self::assertSame([[3, 4]], $immutable->toArray());
            self::assertSame(2154, $immutable->getSrid());
            self::assertSame(4326, $curve->getSrid());
        }
    }

    /** XYM layout preserves every supplied ordinate. */
    public function testXymLayout(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3m\LineString([[0, null, 9]], 4326);
        $arc = new \LongitudeOne\SpatialTypes\Types\Dimension3m\CircularString([[0, null, 9]], 2154);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3m\CompoundCurve([$line, $arc], 0, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYM, $compound->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame([[[0, null, 9]], [[0, null, 9]]], $compound->toArray());
        self::assertSame($compound, $compound->setCurve(0, $arc)->setSrid(3857)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame(CoordinateDimensionEnum::XYM, $compound->getDimension());
        self::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        self::assertSame([[[0, null, 9]], [[0, null, 9]]], $compound->toArray());
        self::assertSame(4326, $line->getSrid());
        self::assertSame(3857, $arc->getPoint(0)->getSrid());
    }

    /** XYZ layout preserves every supplied ordinate. */
    public function testXyzLayout(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension3z\LineString([[0, null, 8]], 4326);
        $arc = new \LongitudeOne\SpatialTypes\Types\Dimension3z\CircularString([[0, null, 8]], 2154);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension3z\CompoundCurve([$line, $arc], 0, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYZ, $compound->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame([[[0, null, 8]], [[0, null, 8]]], $compound->toArray());
        self::assertSame($compound, $compound->setCurve(0, $arc)->setSrid(3857)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame(CoordinateDimensionEnum::XYZ, $compound->getDimension());
        self::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        self::assertSame([[[0, null, 8]], [[0, null, 8]]], $compound->toArray());
        self::assertSame(4326, $line->getSrid());
        self::assertSame(3857, $arc->getPoint(0)->getSrid());
    }

    /** XYZM layout preserves every supplied ordinate. */
    public function testXyzmLayout(): void
    {
        $line = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\LineString([[0, null, 8, 9]], 4326);
        $arc = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\CircularString([[0, null, 8, 9]], 2154);
        $compound = new \LongitudeOne\SpatialTypes\Types\Dimension4zm\CompoundCurve([$line, $arc], 0, SpatialModelEnum::GEOGRAPHY);
        self::assertSame(CoordinateDimensionEnum::XYZM, $compound->getDimension());
        self::assertSame(SpatialModelEnum::GEOGRAPHY, $compound->getFamily());
        self::assertSame([[[0, null, 8, 9]], [[0, null, 8, 9]]], $compound->toArray());
        self::assertSame($compound, $compound->setCurve(0, $arc)->setSrid(3857)->setFamily(SpatialModelEnum::GEOMETRY));
        self::assertSame(CoordinateDimensionEnum::XYZM, $compound->getDimension());
        self::assertSame(GeometryTypeEnum::COMPOUNDCURVE, $compound->getType());
        self::assertSame([[[0, null, 8, 9]], [[0, null, 8, 9]]], $compound->toArray());
        self::assertSame(4326, $line->getSrid());
        self::assertSame(3857, $arc->getPoint(0)->getSrid());
    }
}
